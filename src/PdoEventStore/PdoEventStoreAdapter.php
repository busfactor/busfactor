<?php
declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use BusFactor\EventStore\AdapterInterface;
use BusFactor\EventStore\ConcurrencyException;
use BusFactor\EventStore\InspectorInterface;
use BusFactor\EventStore\StreamNotFoundException;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use BusFactor\EventStream\StreamEventInterface;
use BusFactor\Pdo\PdoInterface;
use BusFactor\Uuid\Uuid;
use DateTimeImmutable;
use Exception;
use PDO;
use PDOException;

class PdoEventStoreAdapter implements AdapterInterface
{
    /** @var PdoInterface */
    private $pdo;

    /** @var Config */
    private $config;

    public function __construct(PdoInterface $pdo, Config $config)
    {
        $this->pdo = $pdo;
        $this->config = $config;
    }

    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream
    {
        $selectColumns = [
            self::sanitizeSqlName($this->config->getAlias('stream_type')),
            self::sanitizeSqlName($this->config->getAlias('stream_id')),
            self::sanitizeSqlName($this->config->getAlias('stream_version')),
            self::sanitizeSqlName($this->config->getAlias('event_id')),
            self::sanitizeSqlName($this->config->getAlias('event_class')),
            self::sanitizeSqlName($this->config->getAlias('event_metadata')),
            self::sanitizeSqlName($this->config->getAlias('event_payload')),
            self::sanitizeSqlName($this->config->getAlias('event_time')),
        ];
        $sql = sprintf(
            'select %s from %s where %s = :streamType and %s = :streamId and %s >= :streamVersion order by %s asc',
            implode(',', $selectColumns),
            self::sanitizeSqlName($this->config->getTable()),
            self::sanitizeSqlName($this->config->getAlias('stream_type')),
            self::sanitizeSqlName($this->config->getAlias('stream_id')),
            self::sanitizeSqlName($this->config->getAlias('stream_version')),
            self::sanitizeSqlName($this->config->getAlias('stream_version'))
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':streamType' => $streamType,
            ':streamId' => $streamId,
            ':streamVersion' => $fromVersion,
        ]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!count($rows) && ($fromVersion === 0)) {
            throw new StreamNotFoundException($streamId);
        }

        $stream = new Stream($streamId, $streamType);
        foreach ($rows as $row) {
            $stream = $stream->withEnvelope($this->buildEventFromRow($row));
        }

        return $stream;
    }

    public function streamExists(string $streamId, string $streamType): bool
    {
        $sql = sprintf(
            'select count(*) event_count from %s where %s = :streamType and %s = :streamId',
            self::sanitizeSqlName($this->config->getTable()),
            self::sanitizeSqlName($this->config->getAlias('stream_type')),
            self::sanitizeSqlName($this->config->getAlias('stream_id'))
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':streamType' => $streamType,
            ':streamId' => $streamId,
        ]);
        $count = $query->fetchAll(PDO::FETCH_ASSOC)[0]['event_count'];
        return (int) $count > 0;
    }

    public function getVersion(string $streamId, string $streamType): int
    {
        $sql = sprintf(
            'select max(%s) max_stream_version from %s where %s = :streamType and %s = :streamId',
            self::sanitizeSqlName($this->config->getAlias('stream_version')),
            self::sanitizeSqlName($this->config->getTable()),
            self::sanitizeSqlName($this->config->getAlias('stream_type')),
            self::sanitizeSqlName($this->config->getAlias('stream_id'))
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':streamType' => $streamType,
            ':streamId' => $streamId,
        ]);
        $version = $query->fetchAll(PDO::FETCH_ASSOC)[0]['max_stream_version'];
        if (!$version) {
            throw new StreamNotFoundException($streamId);
        }

        return (int) $version;
    }

    public function append(Stream $stream, ?int $expectedVersion = null): void
    {
        if ($expectedVersion > 0) {
            $version = $this->getVersion($stream->getStreamId(), $stream->getStreamType());
            if ($version != $expectedVersion) {
                $message = sprintf(
                    'Version for stream %s-%s is %s, expected %s.',
                    $stream->getStreamType(),
                    $stream->getStreamId(),
                    $version,
                    $expectedVersion
                );
                throw new ConcurrencyException($message);
            }
        }

        $insertColumns = [
            self::sanitizeSqlName($this->config->getAlias('stream_type')),
            self::sanitizeSqlName($this->config->getAlias('stream_id')),
            self::sanitizeSqlName($this->config->getAlias('stream_version')),
            self::sanitizeSqlName($this->config->getAlias('event_id')),
            self::sanitizeSqlName($this->config->getAlias('event_class')),
            self::sanitizeSqlName($this->config->getAlias('event_metadata')),
            self::sanitizeSqlName($this->config->getAlias('event_payload')),
            self::sanitizeSqlName($this->config->getAlias('event_time')),
        ];
        $sql = sprintf(
            'insert into %s (%s) values (:streamType, :streamId, :version, :id, :class, :metadata, :payload, :time)',
            self::sanitizeSqlName($this->config->getTable()),
            implode(',', $insertColumns)
        );
        $query = $this->pdo->prepare($sql);
        foreach ($stream->getEnvelopes() as $envelope) {
            /** @var Envelope $envelope */
            $values = $this->buildValuesFromEvent($envelope);
            $metadata = json_encode($values['metadata']);
            $payload = json_encode($values['payload']);
            try {
                $query->execute([
                    ':streamType' => $stream->getStreamType(),
                    ':streamId' => $stream->getStreamId(),
                    ':version' => $values['version'],
                    ':id' => Uuid::new(),
                    ':class' => $values['class'],
                    ':metadata' => $metadata === '[]' ? '{}' : $metadata,
                    ':payload' => $payload === '[]' ? '{}' : $payload,
                    ':time' => $values['time'],
                ]);
                if (json_last_error()) {
                    throw new JsonSerializationException(json_last_error_msg());
                }
            } catch (Exception $e) {
                if ($e instanceof PDOException) {
                    throw new ConcurrencyException(sprintf(
                        'Version %s for stream %s-%s already exists.',
                        $values['version'],
                        $stream->getStreamType(),
                        $stream->getStreamId()
                    ));
                }
            }
        }
    }

    public function inspect(InspectorInterface $inspector): void
    {
        $reverse = $inspector->getFilter()->isReverse();
        $max = $inspector->getFilter()->getLimit();
        $filteredEvents = $inspector->getFilter()->getClasses();

        if (count($filteredEvents)) {
            $questionMarks = str_repeat('?,', count($filteredEvents) - 1) . '?';
            $whereClause = sprintf(
                'where %s in (%s)',
                self::sanitizeSqlName($this->config->getAlias('event_class')),
                $questionMarks
            );
        } else {
            $whereClause = '';
        }

        $limitClause = $max ? 'limit ' . $max : '';
        $sql = sprintf(
            'select * from %s %s order by %s %s %s',
            self::sanitizeSqlName($this->config->getTable()),
            $whereClause,
            self::sanitizeSqlName($this->config->getAlias('sequence')),
            $reverse ? 'desc' : 'asc',
            $limitClause
        );
        $query = $this->pdo->prepare($sql);
        $query->execute($filteredEvents);
        $events = [];
        while ($row = $query->fetch()) {
            $events[] = [
                'stream_id' => $row[self::sanitizeSqlName($this->config->getAlias('stream_id'))],
                'stream_type' => $row[self::sanitizeSqlName($this->config->getAlias('stream_type'))],
                'event' => $this->buildEventFromRow($row),
            ];
        };
        foreach ($events as $event) {
            $inspector->inspect($event['stream_id'], $event['stream_type'], $event['event']);
        }
    }

    public function purge(): void
    {
        $sql = sprintf(
            'delete from %s where 1=1',
            self::sanitizeSqlName($this->config->getTable())
        );
        $this->pdo->exec($sql);
    }

    private function buildEventFromRow(array $row): Envelope
    {
        $version = (int) $row[self::sanitizeSqlName($this->config->getAlias('stream_version'))];
        $metadata = new Metadata(json_decode(
            $row[self::sanitizeSqlName($this->config->getAlias('event_metadata'))],
            true
        ));
        $payload = json_decode($row[self::sanitizeSqlName($this->config->getAlias('event_payload'))], true);
        $class = $row[self::sanitizeSqlName($this->config->getAlias('event_class'))];
        $classRevision = (int) $metadata->get('revision');
        /** @var StreamEventInterface $event */
        $event = $class::deserialize($payload);
        $recordTime = new DateTimeImmutable($row[self::sanitizeSqlName($this->config->getAlias('event_time'))]);

        if ($event::getRevision() != $classRevision) {
            throw new RevisionMismatchException(sprintf(
                'Class revision mismatch for class %s. Got %s, expected %s.',
                $class,
                $classRevision,
                $event::getRevision()
            ));
        }

        if (json_last_error()) {
            throw new JsonSerializationException(json_last_error_msg());
        }

        return Envelope::create($event, $metadata, $version, $recordTime);
    }

    private function buildValuesFromEvent(Envelope $envelope): array
    {
        $values = [
            'version' => $envelope->getVersion(),
            'metadata' => $envelope->getMetadata()
                ->with('revision', $envelope->getEvent()::getRevision())
                ->toArray(),
            'class' => get_class($envelope->getEvent()),
            'payload' => $envelope->getEvent()->serialize(),
            'time' => $envelope->getRecordTime()->format('Y-m-d\TH:i:s.uP'),
        ];

        if (json_last_error()) {
            throw new JsonSerializationException(json_last_error_msg());
        }

        return $values;
    }

    private function sanitizeSqlName(string $value): string
    {
        return preg_replace('/[^a-zA-Z_]*/', '', $value);
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;
use RuntimeException;

class InMemoryEventStoreAdapter implements AdapterInterface
{
    /** @var array */
    private $storage = [];

    /** @var array[] */
    private $chronologicalIndex = [];

    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream
    {
        if ($this->streamExists($streamId, $streamType)) {
            $stream = new Stream($streamId, $streamType);
            /** @var Envelope $envelope */
            foreach ($this->storage[$this->resolveId($streamId, $streamType)]['envelopes'] as $envelope) {
                if ($envelope->getVersion() >= $fromVersion) {
                    $stream = $stream->withEnvelope($envelope);
                }
            }

            return $stream;
        }

        throw StreamNotFoundException::forId($streamId, $streamType);
    }

    public function streamExists(string $streamId, string $streamType): bool
    {
        return array_key_exists($this->resolveId($streamId, $streamType), $this->storage);
    }

    public function getVersion(string $streamId, string $streamType): int
    {
        if ($this->streamExists($streamId, $streamType)) {
            return max(array_keys($this->storage[$this->resolveId($streamId, $streamType)]['envelopes']));
        }

        throw StreamNotFoundException::forId($streamId, $streamType);
    }

    public function append(Stream $stream, ?int $expectedVersion = null): void
    {
        if (count($stream->getEnvelopes()) === 0) {
            return;
        }

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

        $key = $this->resolveId($stream->getStreamId(), $stream->getStreamType());
        if (!array_key_exists($key, $this->storage)) {
            $this->storage[$key] = [
                'id' => $stream->getStreamId(),
                'type' => $stream->getStreamType(),
                'envelopes' => [],
            ];
        }

        /** @var Envelope $envelope */
        foreach ($stream->getEnvelopes() as $envelope) {
            $version = $envelope->getVersion();
            if (isset($this->storage[$key]['envelopes'][$version])) {
                $message = sprintf(
                    'An event with version %d is already recorded for stream ID %s.',
                    $version,
                    $stream->getStreamId()
                );
                throw new ConcurrencyException($message);
            }
            $this->storage[$key]['envelopes'][$version] = $envelope;
            $this->chronologicalIndex[] = [$key, $version];
        }
    }

    public function purge(): void
    {
        $this->storage = [];
        $this->chronologicalIndex = [];
    }

    public function inspect(InspectorInterface $inspector): void
    {
        if ($inspector->getFilter()->isReverse() || $inspector->getFilter()->getLimit()) {
            throw new RuntimeException('Not implemented.');
        }
        foreach ($this->chronologicalIndex as $pair) {
            $key = $pair[0];
            $version = $pair[1];
            /** @var Envelope $envelope */
            $envelope = $this->storage[$key]['envelopes'][$version];
            if (empty($inspector->getFilter()->getClasses())
                || in_array(get_class($envelope->getEvent()), $inspector->getFilter()->getClasses())) {
                $inspector->inspect($this->storage[$key]['id'], $this->storage[$key]['type'], $envelope);
            }
        }
    }

    private function resolveId(string $streamId, string $streamType): string
    {
        return sprintf('%s-%s', $streamType, $streamId);
    }
}

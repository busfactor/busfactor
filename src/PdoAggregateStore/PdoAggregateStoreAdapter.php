<?php
declare(strict_types=1);

namespace BusFactor\PdoAggregateStore;

use BusFactor\Aggregate\AggregateInterface;
use BusFactor\AggregateStore\AdapterInterface;
use BusFactor\AggregateStore\AggregateNotFoundException;
use BusFactor\ObjectSerializer\ObjectSerializerInterface;
use BusFactor\Pdo\PdoInterface;
use PDO;

class PdoAggregateStoreAdapter implements AdapterInterface
{
    /** @var PdoInterface */
    private $pdo;

    /** @var Config */
    private $config;

    /** @var ObjectSerializerInterface */
    private $serialize;

    public function __construct(PdoInterface $pdo, Config $config, ObjectSerializerInterface $serialize)
    {
        $this->pdo = $pdo;
        $this->config = $config;
        $this->serialize = $serialize;
    }

    public function find(string $aggregateId, string $aggregateType): AggregateInterface
    {
        $sql = sprintf(
            'select %s from %s where %s = :aggregateId and %s = :aggregateType',
            $this->config->getAlias('aggregate_payload'),
            $this->config->getTable(),
            $this->config->getAlias('aggregate_id'),
            $this->config->getAlias('aggregate_type'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':aggregateId' => $aggregateId,
            ':aggregateType' => $aggregateType,
        ]);
        $aggregatePayload = $this;
        $query->bindColumn(1, $aggregatePayload, PDO::PARAM_LOB);
        $rows = $query->fetch(PDO::FETCH_BOUND);
        if (!$rows || $aggregatePayload === null) {
            throw AggregateNotFoundException::forAggregate($aggregateId, $aggregateType);
        }
        /** @var AggregateInterface $aggregate */
        $aggregate = $this->serialize->deserialize(
            is_resource($aggregatePayload) ? stream_get_contents($aggregatePayload) : $aggregatePayload
        );

        return $aggregate;
    }

    public function has(string $aggregateId, string $aggregateType): bool
    {
        $sql = sprintf(
            'select count(*) aggregate_count from %s where %s = :aggregateId and %s = :aggregateType',
            $this->config->getTable(),
            $this->config->getAlias('aggregate_id'),
            $this->config->getAlias('aggregate_type'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':aggregateId' => $aggregateId,
            ':aggregateType' => $aggregateType,
        ]);
        return ((int) $query->fetchAll()[0]['aggregate_count'] > 0);
    }

    public function store(AggregateInterface $aggregate): void
    {
        $id = $aggregate->getAggregateId();
        $type = $aggregate::getType();
        $payload = $this->serialize->serialize($aggregate);
        $this->remove($id, $type);

        $insertColumns = [
            $this->config->getAlias('aggregate_id'),
            $this->config->getAlias('aggregate_type'),
            $this->config->getAlias('aggregate_payload'),
        ];
        $sql = sprintf(
            'insert into %s (%s) values (:id, :type, :payload)',
            $this->config->getTable(),
            implode(',', $insertColumns)
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':id' => $id,
            ':type' => $type,
            ':payload' => $payload,
        ]);
    }

    public function remove(string $aggregateId, string $aggregateType): void
    {
        $sql = sprintf(
            'delete from %s where %s = :id and %s = :type',
            $this->config->getTable(),
            $this->config->getAlias('aggregate_id'),
            $this->config->getAlias('aggregate_type'),
        );
        $query = $this->pdo->prepare($sql);
        $query->execute([
            ':id' => $aggregateId,
            ':type' => $aggregateType,
        ]);
    }

    public function purge(): void
    {
        $sql = sprintf('delete from %s where 1=1', $this->config->getTable());
        $this->pdo->exec($sql);
    }
}

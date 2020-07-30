<?php
declare(strict_types=1);

namespace BusFactor\PdoProjectionStore;

use BusFactor\ObjectSerializer\ObjectSerializerInterface;
use BusFactor\Pdo\PdoInterface;
use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\AdapterInterface;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;
use PDO;
use RuntimeException;

class PdoProjectionStoreAdapter implements AdapterInterface
{
    /** @var PdoInterface */
    private $pdo;

    /** @var ObjectSerializerInterface */
    private $serializer;

    /** @var Config */
    private $config;

    public function __construct(PdoInterface $pdo, ObjectSerializerInterface $serializer, Config $config)
    {
        $this->pdo = $pdo;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        $sql = sprintf(
            'select %s from %s where %s = :projectionId and %s = :projectionClass',
            $this->config->getAlias('projection_payload'),
            $this->config->getTable(),
            $this->config->getAlias('projection_id'),
            $this->config->getAlias('projection_class'),
        );
        $query = $this->pdo->prepare($sql);
        $success = $query->execute([
            ':projectionId' => $id,
            ':projectionClass' => $class,
        ]);
        if (!$success) {
            throw new RuntimeException();
        }
        $payload = $this;
        $query->bindColumn(1, $payload, PDO::PARAM_LOB);
        $rows = $query->fetch(PDO::FETCH_BOUND);
        if (!$rows || $payload === null) {
            throw ProjectionNotFoundException::forProjection($class, $id);
        }
        /** @var ProjectionInterface $projection */
        $projection = $this->serializer->deserialize(is_resource($payload) ? stream_get_contents($payload) : $payload);

        return $projection;
    }

    public function findBy(string $class): Generator
    {
        $sql = sprintf(
            'select %s from %s where %s = :projectionClass',
            $this->config->getAlias('projection_payload'),
            $this->config->getTable(),
            $this->config->getAlias('projection_class'),
        );
        $query = $this->pdo->prepare($sql);
        $success = $query->execute([
            ':projectionClass' => $class,
        ]);
        if (!$success) {
            throw new RuntimeException();
        }

        $projectionPayload = $this;
        $query->bindColumn(1, $projectionPayload, PDO::PARAM_LOB);

        while ($query->fetch(PDO::FETCH_BOUND)) {
            $projectionPayload = stream_get_contents($projectionPayload);
            $projection = $this->serializer->deserialize($projectionPayload);

            yield $projection;
        }
    }

    public function has(string $id, string $class): bool
    {
        $sql = sprintf(
            'select %s, %s from %s where %s = :projectionId and %s = :projectionClass',
            $this->config->getAlias('projection_id'),
            $this->config->getAlias('projection_class'),
            $this->config->getTable(),
            $this->config->getAlias('projection_id'),
            $this->config->getAlias('projection_class'),
        );
        $query = $this->pdo->prepare($sql);
        $success = $query->execute([
            ':projectionId' => $id,
            ':projectionClass' => $class,
        ]);
        if (!$success) {
            throw new RuntimeException();
        }
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        return count($rows) > 0;
    }

    public function commit(UnitOfWork $unit): void
    {
        foreach ($unit->getStored() as $projection) {
            $this->store($projection);
        }
        foreach ($unit->getRemoved() as $descriptor) {
            $this->remove($descriptor->getId(), $descriptor->getClass());
        }
    }

    public function purge(): void
    {
        $sql = sprintf('delete from %s where 1=1', $this->config->getTable());
        $this->pdo->exec($sql);
    }

    private function store(ProjectionInterface $projection): void
    {
        $id = $projection->getId();
        $class = get_class($projection);
        $payload = $this->serializer->serialize($projection);
        $this->remove($id, $class);

        $insertColumns = [
            $this->config->getAlias('projection_id'),
            $this->config->getAlias('projection_class'),
            $this->config->getAlias('projection_payload'),
        ];
        $sql = sprintf(
            'insert into %s (%s) values (:projectionId, :projectionClass, :projectionPayload)',
            $this->config->getTable(),
            implode(',', $insertColumns)
        );
        $query = $this->pdo->prepare($sql);
        $success = $query->execute([
            ':projectionId' => $id,
            ':projectionClass' => $class,
            ':projectionPayload' => $payload,
        ]);
        if (!$success) {
            throw new RuntimeException();
        }
    }

    private function remove(string $id, string $class): void
    {
        $sql = sprintf(
            'delete from %s where %s = :projectionId and %s = :projectionClass',
            $this->config->getTable(),
            $this->config->getAlias('projection_id'),
            $this->config->getAlias('projection_class'),
        );
        $query = $this->pdo->prepare($sql);
        $success = $query->execute([
            ':projectionId' => $id,
            ':projectionClass' => $class,
        ]);
        if (!$success) {
            throw new RuntimeException();
        }
    }
}

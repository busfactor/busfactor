<?php
declare(strict_types=1);

namespace BusFactor\AggregateStore;

use BusFactor\Aggregate\AggregateInterface;

class InMemoryAggregateStoreAdapter implements AdapterInterface
{
    /** @var AggregateInterface[][] */
    private $aggregates = [];

    public function find(string $aggregateId, string $aggregateType): AggregateInterface
    {
        if (!$this->has($aggregateId, $aggregateType)) {
            throw new AggregateNotFoundException(sprintf('Aggregate with ID %s not found.', $aggregateId));
        }
        return $this->aggregates[$aggregateType][$aggregateId];
    }

    public function has(string $aggregateId, string $aggregateType): bool
    {
        return isset($this->aggregates[$aggregateType][$aggregateId]);
    }

    public function store(AggregateInterface $aggregate): void
    {
        $this->aggregates[$aggregate::getType()][$aggregate->getAggregateId()] = $aggregate;
    }

    public function remove(string $aggregateId, string $aggregateType): void
    {
        unset($this->aggregates[$aggregateType][$aggregateId]);
    }

    public function purge(): void
    {
        $this->aggregates = [];
    }
}

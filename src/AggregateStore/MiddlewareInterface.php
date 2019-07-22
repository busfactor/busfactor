<?php
declare(strict_types=1);

namespace BusFactor\AggregateStore;

use BusFactor\Aggregate\AggregateInterface;

interface MiddlewareInterface
{
    /** @throws AggregateNotFoundException */
    public function find(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): AggregateInterface;

    public function has(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): bool;

    public function store(AggregateInterface $aggregate, AggregateStoreInterface $next): void;

    public function remove(string $aggregateId, string $aggregateType, AggregateStoreInterface $next): void;

    public function purge(AggregateStoreInterface $next): void;
}

<?php

declare(strict_types=1);

namespace BusFactor\CacheProjectionStoreMiddleware;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\InMemoryProjectionStoreAdapter;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;

class TestProjectionStoreAdapter extends InMemoryProjectionStoreAdapter
{
    /** @var int */
    private $hits = 0;

    public function reset(): void
    {
        $this->hits = 0;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        $this->hits++;
        return parent::find($id, $class);
    }

    public function findBy(string $class): Generator
    {
        $this->hits++;
        yield parent::findBy($class);
    }

    public function has(string $id, string $class): bool
    {
        $this->hits++;
        return parent::has($id, $class);
    }

    public function commit(UnitOfWork $unit): void
    {
        $this->hits++;
        parent::commit($unit);
    }

    public function purge(): void
    {
        $this->hits++;
        parent::purge();
    }
}

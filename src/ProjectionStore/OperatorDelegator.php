<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class OperatorDelegator implements ProjectionStoreInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var ProjectionStoreInterface|null */
    private $next;

    public function __construct(MiddlewareInterface $middleware, ?ProjectionStoreInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        return $this->middleware->find($id, $class, $this->next);
    }

    public function has(string $id, string $class): bool
    {
        return $this->middleware->has($id, $class, $this->next);
    }

    public function store(ProjectionInterface $projection): void
    {
        $this->middleware->store($projection, $this->next);
    }

    public function remove(string $id, string $class): void
    {
        $this->middleware->remove($id, $class, $this->next);
    }

    public function purge(): void
    {
        $this->middleware->purge($this->next);
    }

    public function commit(): void
    {
        $this->middleware->commit($this->next);
    }

    public function rollback(): void
    {
        $this->middleware->rollback($this->next);
    }
}

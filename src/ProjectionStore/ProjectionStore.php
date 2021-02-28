<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class ProjectionStore implements ProjectionStoreInterface
{
    /** @var TransactionalStore */
    private $store;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var ProjectionStoreInterface|null */
    private $chain;

    public function __construct(AdapterInterface $adapter)
    {
        $this->store = new TransactionalStore($adapter);
        $this->chainMiddlewares();
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->store->getAdapter();
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
        $this->chainMiddlewares();
    }

    /** @return MiddlewareInterface[] */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        return $this->chain->find($id, $class);
    }

    public function has(string $id, string $class): bool
    {
        return $this->chain->has($id, $class);
    }

    public function store(ProjectionInterface $projection): void
    {
        $this->chain->store($projection);
    }

    public function remove(string $id, string $class): void
    {
        $this->chain->remove($id, $class);
    }

    public function commit(): void
    {
        $this->chain->commit();
    }

    public function rollback(): void
    {
        $this->chain->rollback();
    }

    public function purge(): void
    {
        $this->chain->purge();
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (ProjectionStoreInterface $carry, MiddlewareInterface $item): ProjectionStoreInterface {
                return new OperatorDelegator($item, $carry);
            },
            $this->store
        );
    }
}

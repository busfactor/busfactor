<?php
declare(strict_types=1);

namespace BusFactor\AggregateStore;

use BusFactor\Aggregate\AggregateInterface;

class AggregateStore implements AggregateStoreInterface
{
    /** @var AdapterInterface */
    private $adapter;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var AggregateStoreInterface */
    private $chain;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->middlewares = [];
        $this->chainMiddlewares();
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
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

    public function find(string $aggregateId, string $aggregateType): AggregateInterface
    {
        return $this->chain->find($aggregateId, $aggregateType);
    }

    public function has(string $aggregateId, string $aggregateType): bool
    {
        return $this->chain->has($aggregateId, $aggregateType);
    }

    public function store(AggregateInterface $aggregate): void
    {
        $this->chain->store($aggregate);
    }

    public function remove(string $aggregateId, string $aggregateType): void
    {
        $this->chain->remove($aggregateId, $aggregateType);
    }

    public function purge(): void
    {
        $this->chain->purge();
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (AggregateStoreInterface $carry, MiddlewareInterface $item): AggregateStoreInterface {
                return new StoreDelegator($item, $carry);
            },
            $this->adapter
        );
    }
}

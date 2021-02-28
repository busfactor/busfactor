<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Stream;

class EventStore implements EventStoreInterface
{
    /** @var AdapterInterface */
    private $adapter;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var EventStoreInterface */
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

    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream
    {
        return $this->chain->fetch($streamId, $streamType, $fromVersion);
    }

    public function streamExists(string $streamId, string $streamType): bool
    {
        return $this->chain->streamExists($streamId, $streamType);
    }

    public function getVersion(string $streamId, string $streamType): int
    {
        return $this->chain->getVersion($streamId, $streamType);
    }

    public function append(Stream $stream, ?int $expectedVersion = null): void
    {
        $this->chain->append($stream, $expectedVersion);
    }

    public function inspect(InspectorInterface $inspector): void
    {
        $this->chain->inspect($inspector);
    }

    public function purge(): void
    {
        $this->chain->purge();
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (EventStoreInterface $carry, MiddlewareInterface $item): EventStoreInterface {
                return new StoreDelegator($item, $carry);
            },
            $this->adapter
        );
    }
}

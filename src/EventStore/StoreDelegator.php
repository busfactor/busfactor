<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Stream;

class StoreDelegator implements EventStoreInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var EventStoreInterface|null */
    private $next;

    public function __construct(MiddlewareInterface $middleware, ?EventStoreInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream
    {
        return $this->middleware->fetch($streamId, $streamType, $fromVersion, $this->next);
    }

    public function streamExists(string $streamId, string $streamType): bool
    {
        return $this->middleware->streamExists($streamId, $streamType, $this->next);
    }

    public function getVersion(string $streamId, string $streamType): int
    {
        return $this->middleware->getVersion($streamId, $streamType, $this->next);
    }

    public function append(Stream $stream, ?int $expectedVersion = null): void
    {
        $this->middleware->append($stream, $expectedVersion, $this->next);
    }

    public function inspect(InspectorInterface $inspector): void
    {
        $this->middleware->inspect($inspector, $this->next);
    }

    public function purge(): void
    {
        $this->middleware->purge($this->next);
    }
}

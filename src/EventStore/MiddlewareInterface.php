<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Stream;

interface MiddlewareInterface
{
    public function fetch(string $streamId, string $streamType, int $fromVersion, EventStoreInterface $next): Stream;

    public function streamExists(string $streamId, string $streamType, EventStoreInterface $next): bool;

    public function getVersion(string $streamId, string $streamType, EventStoreInterface $next): int;

    public function append(Stream $stream, ?int $expectedVersion, EventStoreInterface $next): void;

    public function inspect(InspectorInterface $inspector, EventStoreInterface $next): void;

    public function purge(EventStoreInterface $next): void;
}

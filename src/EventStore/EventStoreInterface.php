<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Stream;

interface EventStoreInterface
{
    /**
     * @throws StreamNotFoundException if no stream was found.
     */
    public function fetch(string $streamId, string $streamType, int $fromVersion = 0): Stream;

    public function streamExists(string $streamId, string $streamType): bool;

    /**
     * @throws StreamNotFoundException if no stream was found.
     */
    public function getVersion(string $streamId, string $streamType): int;

    /**
     * @throws ConcurrencyException
     */
    public function append(Stream $stream, ?int $expectedVersion = null): void;

    public function inspect(InspectorInterface $inspector): void;

    public function purge(): void;
}

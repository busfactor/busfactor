<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Stream;

class TestMiddleware implements MiddlewareInterface
{
    /** @var string */
    private $name;

    /** @var array */
    private $output;

    public function __construct(string $name, array &$output)
    {
        $this->name = $name;
        $this->output = &$output;
    }

    public function fetch(string $streamId, string $streamType, int $fromVersion, EventStoreInterface $next): Stream
    {
        $this->output[] = 'before fetch ' . $this->name;
        $stream = $next->fetch($streamId, $streamType, $fromVersion);
        $this->output[] = 'after fetch ' . $this->name;
        return $stream;
    }

    public function streamExists(string $streamId, string $streamType, EventStoreInterface $next): bool
    {
        $this->output[] = 'before streamExists ' . $this->name;
        $exists = $next->streamExists($streamId, $streamType);
        $this->output[] = 'after streamExists ' . $this->name;
        return $exists;
    }

    public function getVersion(string $streamId, string $streamType, EventStoreInterface $next): int
    {
        $this->output[] = 'before getVersion ' . $this->name;
        $version = $next->getVersion($streamId, $streamType);
        $this->output[] = 'after getVersion ' . $this->name;
        return $version;
    }

    public function append(Stream $stream, ?int $expectedVersion, EventStoreInterface $next): void
    {
        $this->output[] = 'before append ' . $this->name;
        $next->append($stream, $expectedVersion);
        $this->output[] = 'after append ' . $this->name;
    }

    public function inspect(InspectorInterface $inspector, EventStoreInterface $next): void
    {
        $this->output[] = 'before inspect ' . $this->name;
        $next->inspect($inspector);
        $this->output[] = 'after inspect ' . $this->name;
    }

    public function purge(EventStoreInterface $next): void
    {
        $this->output[] = 'before purge ' . $this->name;
        $next->purge();
        $this->output[] = 'after purge ' . $this->name;
    }
}

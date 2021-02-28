<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventStoreMiddleware;

use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\InspectorInterface;
use BusFactor\EventStore\MiddlewareInterface;
use BusFactor\EventStream\Stream;
use BusFactor\StreamEnricher\StreamEnricherInterface;

class StreamEnricherEventStoreMiddleware implements MiddlewareInterface
{
    /** @var StreamEnricherInterface */
    private $enricher;

    public function __construct(StreamEnricherInterface $enricher)
    {
        $this->enricher = $enricher;
    }

    public function fetch(string $streamId, string $streamType, int $fromVersion, EventStoreInterface $next): Stream
    {
        return $next->fetch($streamId, $streamType, $fromVersion);
    }

    public function streamExists(string $streamId, string $streamType, EventStoreInterface $next): bool
    {
        return $next->streamExists($streamId, $streamType);
    }

    public function getVersion(string $streamId, string $streamType, EventStoreInterface $next): int
    {
        return $next->getVersion($streamId, $streamType);
    }

    public function append(Stream $stream, ?int $expectedVersion, EventStoreInterface $next): void
    {
        $next->append($this->enricher->enrich($stream), $expectedVersion);
    }

    public function inspect(InspectorInterface $inspector, EventStoreInterface $next): void
    {
        $next->inspect($inspector);
    }

    public function purge(EventStoreInterface $next): void
    {
        $next->purge();
    }
}

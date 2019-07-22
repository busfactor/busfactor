<?php
declare(strict_types=1);

namespace BusFactor\StreamEnricherEventBusMiddleware;

use BusFactor\EventBus\EventStreamPublisherInterface;
use BusFactor\EventBus\MiddlewareInterface;
use BusFactor\EventStream\Stream;
use BusFactor\StreamEnricher\StreamEnricherInterface;

class StreamEnricherEventBusMiddleware implements MiddlewareInterface
{
    /** @var StreamEnricherInterface */
    private $enricher;

    public function __construct(StreamEnricherInterface $enricher)
    {
        $this->enricher = $enricher;
    }

    public function publish(Stream $stream, EventStreamPublisherInterface $next): void
    {
        $next->publish($this->enricher->enrich($stream));
    }
}

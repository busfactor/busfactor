<?php
declare(strict_types=1);

namespace BusFactor\StreamPublishingInspection;

use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\Filter;
use BusFactor\EventStore\InspectorInterface;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;

class Inspector implements InspectorInterface
{
    /** @var EventBusInterface */
    private $eventBus;

    /** @var callable|null */
    private $before;

    /** @var callable|null */
    private $after;

    public function __construct(EventBusInterface $eventBus, ?callable $before = null, ?callable $after = null)
    {
        $this->eventBus = $eventBus;
        $this->before = $before;
        $this->after = $after;
    }

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $aggregateId, string $streamType, Envelope $envelope): void
    {
        $stream = new Stream($aggregateId, $streamType);
        $stream = $stream->withEnvelope($envelope);
        if ($this->before) {
            $before = $this->before;
            $before($aggregateId, $envelope);
        }
        $this->eventBus->publish($stream);
        if ($this->after) {
            $after = $this->after;
            $after($aggregateId, $envelope);
        }
    }
}

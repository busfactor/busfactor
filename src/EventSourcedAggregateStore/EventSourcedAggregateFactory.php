<?php
declare(strict_types=1);

namespace BusFactor\EventSourcedAggregateStore;

use BusFactor\EventSourcedAggregate\EventSourcedAggregateInterface;
use BusFactor\EventStream\Stream;
use InvalidArgumentException;

class EventSourcedAggregateFactory
{
    /** @var string */
    private $aggregateRootClass;

    public function __construct(string $aggregateRootClass)
    {
        if (!in_array(EventSourcedAggregateInterface::class, class_implements($aggregateRootClass))) {
            $message = 'Class ' . $aggregateRootClass . ' must implement ' . EventSourcedAggregateInterface::class;
            throw new InvalidArgumentException($message);
        }
        $this->aggregateRootClass = $aggregateRootClass;
    }

    public function getAggregateRootClass(): string
    {
        return $this->aggregateRootClass;
    }

    public function rebuildFromStream(Stream $stream): EventSourcedAggregateInterface
    {
        $class = $this->aggregateRootClass;
        /** @var EventSourcedAggregateInterface $aggregate */
        $aggregate = new $class($stream->getStreamId());
        $aggregate->replayStream($stream);
        return $aggregate;
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\EventSourcedAggregateStore;

use BusFactor\Aggregate\AggregateInterface;
use BusFactor\AggregateStore\AdapterInterface;
use BusFactor\AggregateStore\AggregateNotFoundException;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventSourcedAggregate\EventSourcedAggregateInterface;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\StreamNotFoundException;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;
use InvalidArgumentException;
use RuntimeException;

class EventSourcedAggregateStoreAdapter implements AdapterInterface
{
    /** @var EventSourcedAggregateFactory */
    private $aggregateFactory;

    /** @var EventStoreInterface */
    private $eventStore;

    /** @var EventBusInterface */
    private $eventBus;

    public function __construct(
        EventSourcedAggregateFactory $aggregateFactory,
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus
    ) {
        $this->aggregateFactory = $aggregateFactory;
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function find(string $aggregateId, string $aggregateType): AggregateInterface
    {
        if ($this->getExpectedAggregateType() !== $aggregateType) {
            throw new InvalidArgumentException('Unexpected aggregate type.');
        }
        try {
            $stream = $this->eventStore->fetch($aggregateId, $aggregateType);
        } catch (StreamNotFoundException $e) {
            throw AggregateNotFoundException::forAggregate($aggregateId, $aggregateType, $e);
        }
        $class = $this->aggregateFactory->getAggregateRootClass();
        /** @var EventSourcedAggregateInterface $aggregate */
        $aggregate = new $class($stream->getStreamId());
        $aggregate->replayStream($stream);

        return $aggregate;
    }

    public function has(string $aggregateId, string $aggregateType): bool
    {
        return $this->eventStore->streamExists($aggregateId, $aggregateType);
    }

    public function store(AggregateInterface $aggregate): void
    {
        $stream = new Stream($aggregate->getAggregateId(), $aggregate::getType());
        $recordedEvents = $aggregate->pullNewEvents();
        foreach ($recordedEvents as $recordedEvent) {
            $stream = $stream->withEnvelope(Envelope::fromRecordedEvent($recordedEvent));
        }
        $this->eventStore->append($stream);
        $this->eventBus->publish($stream);
    }

    public function remove(string $aggregateId, string $aggregateType): void
    {
        throw new RuntimeException('Not implemented.');
    }

    public function purge(): void
    {
        throw new RuntimeException('Not implemented.');
    }

    private function getExpectedAggregateType(): string
    {
        /** @var AggregateInterface $class */
        $class = $this->aggregateFactory->getAggregateRootClass();
        return $class::getType();
    }
}

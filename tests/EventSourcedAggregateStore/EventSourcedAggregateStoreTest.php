<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregateStore;

use BusFactor\AggregateStore\AggregateStore;
use BusFactor\EventBus\EventBus;
use BusFactor\EventBus\EventHandlerInterface;
use BusFactor\EventStore\EventStoreInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventSourcedAggregateStoreTest extends TestCase
{
    /** @test */
    public function it_persists_with_event_store_and_dispatches_new_events(): void
    {
        /** @var EventStoreInterface|MockObject $eventStore */
        $eventStore = $this->createMock(EventStoreInterface::class);
        $eventStore->expects($this->once())->method('append');

        $eventBus = new EventBus();
        /** @var EventHandlerInterface|MockObject $handler */
        $handler = $this->createMock(EventHandlerInterface::class);
        $handler->expects($this->exactly(3))->method('handle');
        $eventBus->subscribe(TestEvent::class, $handler);

        $aggregateStore = new AggregateStore(new EventSourcedAggregateStoreAdapter(
            new EventSourcedAggregateFactory(TestAggregate::class),
            $eventStore,
            $eventBus
        ));

        $aggregate = TestAggregate::create('123');
        $aggregate->touch();
        $aggregate->touch();

        $aggregateStore->store($aggregate);
    }
}

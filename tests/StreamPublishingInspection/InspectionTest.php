<?php

declare(strict_types=1);

namespace BusFactor\StreamPublishingInspection;

use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class InspectionTest extends TestCase
{
    /** @test */
    public function it_publishes_inspected_events(): void
    {
        /** @var EventBusInterface|MockObject $eventBus */
        $eventBus = $this->createMock(EventBusInterface::class);
        $eventBus->expects($this->exactly(3))->method('publish');

        $adapter = new InMemoryEventStoreAdapter();
        $adapter->append(
            (new Stream('123', 'type'))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 1))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 2))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 3))
        );

        (new Inspection($adapter, $eventBus))->start();
    }
}

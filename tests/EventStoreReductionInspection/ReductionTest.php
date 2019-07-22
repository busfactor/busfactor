<?php
declare(strict_types=1);

namespace BusFactor\EventStoreReductionInspection;

use BusFactor\EventStore\EventStore;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use PHPUnit\Framework\TestCase;

class ReductionTest extends TestCase
{
    /** @test */
    public function it_reduces_events(): void
    {
        $eventStore = new EventStore(new InMemoryEventStoreAdapter());
        $eventStore->append(
            (new Stream('123', 'type'))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 1))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 2))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 3))
        );
        $reduction = new EventStoreReductionInspection($eventStore->getAdapter());

        $this->assertEquals(3, $reduction->inspect(new TestReducer()));
    }
}

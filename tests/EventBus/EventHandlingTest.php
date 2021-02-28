<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use PHPUnit\Framework\TestCase;

class EventHandlingTest extends TestCase
{
    /** @test */
    public function it_publishes_stream_to_subscribed_handlers(): void
    {
        $bus = new EventBus();
        $handler1 = new TestEventHandler1();
        $handler2 = new TestEventHandler2();

        $bus->subscribe(TestEvent1::class, $handler1);
        $bus->subscribe(TestEvent2::class, $handler2);

        $stream = (new Stream('123', 'type'))
            ->withEnvelope(Envelope::createNow(new TestEvent1(), new Metadata(), 1))
            ->withEnvelope(Envelope::createNow(new TestEvent2(), new Metadata(), 2));

        $this->assertEmpty($handler1->getHandledEvents());
        $this->assertEmpty($handler2->getHandledEvents());

        $bus->publish($stream);

        $this->assertCount(1, $handler1->getHandledEvents());
        $this->assertInstanceOf(TestEvent1::class, $handler1->getHandledEvents()[0]);

        $this->assertCount(1, $handler2->getHandledEvents());
        $this->assertInstanceOf(TestEvent2::class, $handler2->getHandledEvents()[0]);
    }
}

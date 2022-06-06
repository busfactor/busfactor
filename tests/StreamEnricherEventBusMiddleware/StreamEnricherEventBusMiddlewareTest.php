<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventBusMiddleware;

use BusFactor\EventBus\EventBus;
use BusFactor\EventBus\EventStreamPublisherInterface;
use BusFactor\EventBus\MiddlewareInterface;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use PHPUnit\Framework\TestCase;

class StreamEnricherEventBusMiddlewareTest extends TestCase
{
    /** @test */
    public function it_enriches_stream(): void
    {
        $mw = new class () implements MiddlewareInterface {
            /** @var Metadata */
            public $metadata;

            public function publish(Stream $stream, EventStreamPublisherInterface $next): void
            {
                $this->metadata = $stream->getEnvelopes()[0]->getMetadata();
                $next->publish($stream);
            }
        };

        $bus = new EventBus();
        $bus->addMiddleware($mw);
        $bus->addMiddleware(new StreamEnricherEventBusMiddleware(new TestEnricher()));

        $stream = (new Stream('123', 'type'))
            ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 1));

        $bus->publish($stream);

        $this->assertEquals($mw->metadata->toArray(), ['foo' => 'bar']);
    }
}

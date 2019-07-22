<?php
declare(strict_types=1);

namespace BusFactor\EventStream;

use BusFactor\Aggregate\EventInterface;
use BusFactor\Aggregate\RecordedEvent;
use PHPUnit\Framework\TestCase;

class EnvelopeTest extends TestCase
{
    /** @test */
    public function it_creates_envelope_from_recorded_event(): void
    {
        $event = new TestEvent('abc', 123, []);
        $envelope = Envelope::fromRecordedEvent(new RecordedEvent($event, 1));

        $this->assertSame($event, $envelope->getEvent());
    }

    /** @test */
    public function it_throws_exception_when_not_instance_of_stream_recorded_event(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $event = new class implements EventInterface {
        };

        Envelope::fromRecordedEvent(new RecordedEvent($event, 1));
    }

    /** @test */
    public function record_time_includes_microseconds(): void
    {
        $microseconds1 = Envelope::createNow(
            new TestEvent('abc', 123, []),
            new Metadata(),
            1
        )->getRecordTime()->format('u');
        $microseconds2 = Envelope::createNow(
            new TestEvent('abc', 123, []),
            new Metadata(),
            1
        )->getRecordTime()->format('u');

        $this->assertEquals(6, strlen($microseconds1));
        $this->assertEquals(6, strlen($microseconds2));
        $this->assertNotEquals($microseconds1, $microseconds2);
    }
}

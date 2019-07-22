<?php
declare(strict_types=1);

namespace BusFactor\Aggregate;

use PHPUnit\Framework\TestCase;

class RecordedEventTest extends TestCase
{
    /** @test */
    public function record_time_includes_microseconds(): void
    {
        $microseconds1 = (new RecordedEvent(new TestEvent(), 1))->getRecordTime()->format('u');
        $microseconds2 = (new RecordedEvent(new TestEvent(), 1))->getRecordTime()->format('u');

        $this->assertEquals(6, strlen($microseconds1));
        $this->assertEquals(6, strlen($microseconds2));
        $this->assertNotEquals($microseconds1, $microseconds2);
    }
}

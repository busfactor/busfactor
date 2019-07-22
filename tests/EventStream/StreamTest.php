<?php
declare(strict_types=1);

namespace BusFactor\EventStream;

use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    /** @test */
    public function it_returns_lowest_and_highest_version(): void
    {
        $stream = (new Stream('1', 'foo'))
            ->withEnvelope(Envelope::createNow(new TestEvent('abc', 123, []), new Metadata(), 1))
            ->withEnvelope(Envelope::createNow(new TestEvent('abc', 123, []), new Metadata(), 2))
            ->withEnvelope(Envelope::createNow(new TestEvent('abc', 123, []), new Metadata(), 3));

        $this->assertEquals(1, $stream->getLowestVersion());
        $this->assertEquals(3, $stream->getHighestVersion());
    }
}

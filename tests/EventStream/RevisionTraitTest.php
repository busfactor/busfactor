<?php
declare(strict_types=1);

namespace BusFactor\EventStream;

use PHPUnit\Framework\TestCase;

class RevisionTraitTest extends TestCase
{
    /** @test */
    public function it_returns_revision(): void
    {
        $this->assertSame(1, TestEvent::getRevision());
    }
}

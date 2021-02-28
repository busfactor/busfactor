<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class AggregateEntityTest extends TestCase
{
    /** @test */
    public function it_cannot_apply_event_when_not_attached_to_an_aggregate_root(): void
    {
        $this->expectException(RuntimeException::class);

        (new TestAggregateEntity())->action();
    }
}

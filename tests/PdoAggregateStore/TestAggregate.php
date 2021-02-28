<?php

declare(strict_types=1);

namespace BusFactor\PdoAggregateStore;

use BusFactor\Aggregate\AggregateInterface;
use BusFactor\Aggregate\AggregateRootTrait;

class TestAggregate implements AggregateInterface
{
    use AggregateRootTrait;

    public static function getType(): string
    {
        return 'test-aggregate';
    }
}

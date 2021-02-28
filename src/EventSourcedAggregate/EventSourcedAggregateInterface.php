<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

use BusFactor\Aggregate\AggregateInterface;
use BusFactor\EventStream\Stream;

interface EventSourcedAggregateInterface extends AggregateInterface
{
    public function replayStream(Stream $stream): void;
}

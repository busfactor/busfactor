<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

use BusFactor\Aggregate\AggregateRootTrait;
use BusFactor\Aggregate\RecordedEvent;
use BusFactor\EventStream\Stream;

trait EventSourcedAggregateRootTrait
{
    use AggregateRootTrait;

    public function replayStream(Stream $stream): void
    {
        foreach ($stream->getEnvelopes() as $envelope) {
            $this->aggregateRootTrait_version++;
            $this->__handle(
                (new RecordedEvent($envelope->getEvent(), $envelope->getVersion()))
                    ->withRecordTime($envelope->getRecordTime())
            );
        }
    }
}

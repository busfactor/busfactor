<?php
declare(strict_types=1);

namespace BusFactor\EventStoreReductionInspection;

use BusFactor\EventStore\Filter;
use BusFactor\EventStream\Envelope;

class TestReducer implements ReductionInspectorInterface
{
    /** @var int */
    private $eventCount = 0;

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $streamId, string $streamType, Envelope $envelope): void
    {
        $this->eventCount++;
    }

    /** @return mixed */
    public function getResult()
    {
        return $this->eventCount;
    }

    public function reset(): void
    {
        $this->eventCount = 0;
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Envelope;

class TestInspector implements InspectorInterface
{
    /** @var Envelope[] */
    private $inspectedEvents = [];

    public function getFilter(): Filter
    {
        return new Filter();
    }

    public function inspect(string $streamId, string $streamType, Envelope $envelope): void
    {
        $this->inspectedEvents[] = $envelope;
    }

    public function getInspectedEvents(): array
    {
        return $this->inspectedEvents;
    }
}

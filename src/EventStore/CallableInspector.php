<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Envelope;

class CallableInspector implements InspectorInterface
{
    /** @var callable */
    private $callable;

    /** @var Filter */
    private $filter;

    public function __construct(callable $callable, Filter $filter)
    {
        $this->callable = $callable;
        $this->filter = $filter;
    }

    public function getFilter(): Filter
    {
        return $this->filter;
    }

    public function inspect(string $streamId, string $streamType, Envelope $envelope): void
    {
        $callable = $this->callable;
        $callable($streamId, $envelope);
    }
}

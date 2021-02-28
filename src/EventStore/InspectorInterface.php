<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\EventStream\Envelope;

interface InspectorInterface
{
    public function getFilter(): Filter;

    public function inspect(string $streamId, string $streamType, Envelope $envelope): void;
}

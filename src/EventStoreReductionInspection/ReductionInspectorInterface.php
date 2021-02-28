<?php

declare(strict_types=1);

namespace BusFactor\EventStoreReductionInspection;

use BusFactor\EventStore\InspectorInterface;

interface ReductionInspectorInterface extends InspectorInterface
{
    /** @return mixed */
    public function getResult();

    public function reset(): void;
}

<?php

declare(strict_types=1);

namespace BusFactor\EventStoreReductionInspection;

use BusFactor\EventStore\AdapterInterface;

class EventStoreReductionInspection
{
    /** @var AdapterInterface */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /** @return mixed */
    public function inspect(ReductionInspectorInterface $reducer)
    {
        $reducer->reset();
        $this->adapter->inspect($reducer);
        return $reducer->getResult();
    }
}

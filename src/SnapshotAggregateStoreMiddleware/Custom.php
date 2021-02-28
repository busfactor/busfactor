<?php

declare(strict_types=1);

namespace BusFactor\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

class Custom implements StrategyInterface
{
    /** @var callable */
    private $mustSnapshot;

    /** @var callable */
    private $mustLoad;

    public function __construct(callable $mustSnapshot, callable $mustLoad)
    {
        $this->mustSnapshot = $mustSnapshot;
        $this->mustLoad = $mustLoad;
    }

    public function mustSnapshot(AggregateInterface $aggregate): bool
    {
        $mustSnapshot = $this->mustSnapshot;
        return (bool) $mustSnapshot($aggregate);
    }

    public function mustLoad(): bool
    {
        $mustLoad = $this->mustLoad;
        return (bool) $mustLoad();
    }
}

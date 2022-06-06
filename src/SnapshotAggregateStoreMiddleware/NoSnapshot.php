<?php

declare(strict_types=1);

namespace BusFactor\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

class NoSnapshot implements StrategyInterface
{
    public function mustSnapshot(AggregateInterface $aggregate): bool
    {
        return false;
    }

    public function mustLoad(): bool
    {
        return false;
    }
}

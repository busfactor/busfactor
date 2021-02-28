<?php

declare(strict_types=1);

namespace BusFactor\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

class Always implements StrategyInterface
{
    public function mustSnapshot(AggregateInterface $aggregate): bool
    {
        return true;
    }

    public function mustLoad(): bool
    {
        return true;
    }
}

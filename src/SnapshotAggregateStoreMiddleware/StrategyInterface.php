<?php

declare(strict_types=1);

namespace BusFactor\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

interface StrategyInterface
{
    public function mustSnapshot(AggregateInterface $aggregate): bool;

    public function mustLoad(): bool;
}

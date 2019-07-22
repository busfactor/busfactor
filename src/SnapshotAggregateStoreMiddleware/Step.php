<?php
declare(strict_types=1);

namespace BusFactor\SnapshotAggregateStoreMiddleware;

use BusFactor\Aggregate\AggregateInterface;

class Step implements StrategyInterface
{
    /** @var int */
    private $step;

    public function __construct(int $step = 100)
    {
        $this->step = abs($step);
    }

    public function mustSnapshot(AggregateInterface $aggregate): bool
    {
        $version = $aggregate->getVersion();
        $newEvents = count($aggregate->peekNewEvents());
        $versions = range($version - $newEvents, $version);
        foreach ($versions as $version) {
            if (($version % $this->step) === 0) {
                return true;
            }
        }
        return false;
    }

    public function mustLoad(): bool
    {
        return true;
    }
}

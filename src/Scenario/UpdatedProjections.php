<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\Projection\ProjectionInterface;
use Countable;

class UpdatedProjections implements Countable
{
    /** @var ProjectionInterface[] */
    private $updatedProjections;

    public function __construct(array $updatedProjections)
    {
        $this->updatedProjections = $updatedProjections;
    }

    public function count(): int
    {
        return count($this->updatedProjections);
    }

    /** @return ProjectionInterface[] */
    public function getAll(): array
    {
        return $this->updatedProjections;
    }

    /** @return ProjectionInterface[] */
    public function getAllOf(string $projectionClass): array
    {
        $projections = [];
        foreach ($this->updatedProjections as $projection) {
            if (get_class($projection) === $projectionClass) {
                $projections[] = $projection;
            }
        }
        return $projections;
    }
}

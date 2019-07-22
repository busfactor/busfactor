<?php
declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\UpdatedProjections;
use PHPUnit\Framework\Constraint\Constraint;

class ProjectionsMustCount extends Constraint
{
    /** @var int */
    private $count;

    public function __construct(int $count)
    {
        parent::__construct();
        $this->count = $count;
    }

    public function toString(): string
    {
        return "must have {$this->count} projection(s)";
    }

    /**
     * @param UpdatedProjections $updatedProjections
     */
    public function matches($updatedProjections): bool
    {
        return count($updatedProjections) === $this->count;
    }
}

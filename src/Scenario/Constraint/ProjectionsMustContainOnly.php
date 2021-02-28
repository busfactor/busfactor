<?php

declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\UpdatedProjections;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class ProjectionsMustContainOnly extends Constraint
{
    /** @var string */
    private $projectionClass;

    public function __construct(string $projectionClass)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->projectionClass = $projectionClass;
    }

    public function toString(): string
    {
        return "must contains only {$this->projectionClass}";
    }

    /**
     * @param UpdatedProjections $updatedProjections
     */
    public function matches($updatedProjections): bool
    {
        $result = true;
        foreach ($updatedProjections->getAll() as $projection) {
            $result = $result && (get_class($projection) === $this->projectionClass);
        }
        return $result;
    }
}

<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use RuntimeException;

class Filter
{
    /** @var string[] */
    private $onlyClasses = [];

    /** @var bool */
    private $reverse;

    /** @var int */
    private $limit;

    public function __construct(?bool $reverse = false, ?int $limit = 0, string ...$onlyClasses)
    {
        $this->reverse = $reverse;
        $this->limit = $limit;
        foreach ($onlyClasses as $class) {
            if (!class_exists($class)) {
                throw new RuntimeException('Class not found.');
            }
        }
        $this->onlyClasses = $onlyClasses;
    }

    public function isReverse(): bool
    {
        return $this->reverse;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getClasses(): array
    {
        return $this->onlyClasses;
    }
}

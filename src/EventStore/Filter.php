<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

class Filter
{
    private array $filters = [];

    public function get(string $name)
    {
        return $this->filters[$name] ?? null;
    }

    public function with(string $name, $value): self
    {
        $clone = clone $this;
        $clone->filters[$name] = $value;
        return $clone;
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\PdoAggregateStore;

use InvalidArgumentException;

class Config
{
    /** @var string */
    private $table = 'aggregate_store';

    /** @var string[] */
    private $aliases = [

        'aggregate_id' => 'aggregate_id',
        'aggregate_type' => 'aggregate_type',
        'aggregate_payload' => 'aggregate_payload',
    ];

    public function withTable(string $table): self
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    public function withAlias(string $column, string $alias): self
    {
        if (!array_key_exists($column, $this->aliases)) {
            throw new InvalidArgumentException();
        }
        $clone = clone $this;
        $clone->aliases[$column] = $alias;
        return $clone;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getAlias(string $column): string
    {
        if (!array_key_exists($column, $this->aliases)) {
            throw new InvalidArgumentException();
        }
        return $this->aliases[$column];
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use InvalidArgumentException;

class Config
{
    /** @var string */
    private $table = 'event_store';

    /** @var string[] */
    private $aliases = [
        'sequence' => 'sequence',
        'stream_type' => 'stream_type',
        'stream_id' => 'stream_id',
        'stream_version' => 'stream_version',
        'event_id' => 'event_id',
        'event_class' => 'event_class',
        'event_metadata' => 'event_metadata',
        'event_payload' => 'event_payload',
        'event_time' => 'event_time',
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

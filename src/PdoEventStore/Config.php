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

    /** @var bool */
    private $eventBuffering = true;

    public function withEventBuffering(bool $active): self
    {
        $clone = clone $this;
        $clone->eventBuffering = $active;
        return $clone;
    }

    public function withTable(string $table): self
    {
        $clone = clone $this;
        $clone->table = $this->sanitizeSqlName($table);
        return $clone;
    }

    public function withAlias(string $column, string $alias): self
    {
        if (!array_key_exists($column, $this->aliases)) {
            throw new InvalidArgumentException();
        }
        $clone = clone $this;
        $clone->aliases[$column] = $this->sanitizeSqlName($alias);
        return $clone;
    }

    public function getEventBuffering(): bool
    {
        return $this->eventBuffering;
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

    private function sanitizeSqlName(string $value): string
    {
        return preg_replace('/[^a-zA-Z_]*/', '', $value);
    }
}

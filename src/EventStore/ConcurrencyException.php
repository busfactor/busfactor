<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

class ConcurrencyException extends EventStoreException
{
    public function getStatus(): int
    {
        return 409;
    }

    public function getType(): string
    {
        return 'conflict';
    }

    public function getTitle(): string
    {
        return 'conflict';
    }
}

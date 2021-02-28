<?php

declare(strict_types=1);

namespace BusFactor\EventStore;

use BusFactor\Problem\ProblemDetailsAwareInterface;
use BusFactor\Problem\ProblemDetailsAwareTrait;
use Exception;

class EventStoreException extends Exception implements ProblemDetailsAwareInterface
{
    use ProblemDetailsAwareTrait;

    public function getStatus(): int
    {
        return 500;
    }

    public function getType(): string
    {
        return 'event-store-exception';
    }

    public function getTitle(): string
    {
        return 'event-store-exception';
    }
}

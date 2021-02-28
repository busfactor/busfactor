<?php

declare(strict_types=1);

namespace BusFactor\AggregateStore;

use BusFactor\Problem\ProblemDetailsAwareInterface;
use BusFactor\Problem\ProblemDetailsAwareTrait;
use Exception;

class AggregateNotFoundException extends Exception implements ProblemDetailsAwareInterface
{
    use ProblemDetailsAwareTrait;

    public static function forAggregate(string $aggregateId, string $aggregateType, ?Exception $previous = null): self
    {
        $message = sprintf('Aggregate of type [%s] with ID [%s] not found.', $aggregateType, $aggregateId);

        return new static($message, 0, $previous);
    }

    public function getStatus(): int
    {
        return 404;
    }

    public function getType(): string
    {
        return 'aggregate-not-found';
    }

    public function getTitle(): string
    {
        return 'aggregate-not-found';
    }

    public function getDetail(): string
    {
        return $this->message;
    }
}

<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use BusFactor\Problem\ProblemDetailsAwareInterface;
use BusFactor\Problem\ProblemDetailsAwareTrait;
use Exception;

class ProjectionNotFoundException extends Exception implements ProblemDetailsAwareInterface
{
    use ProblemDetailsAwareTrait;

    public static function forProjection(string $class, string $id): self
    {
        $message = sprintf('Projection with class %s and ID %s not found.', $class, $id);

        return new static($message);
    }

    public function getStatus(): int
    {
        return 404;
    }

    public function getType(): string
    {
        return 'projection-not-found';
    }

    public function getTitle(): string
    {
        return 'projection-not-found';
    }

    public function getDetail(): string
    {
        return $this->message;
    }
}

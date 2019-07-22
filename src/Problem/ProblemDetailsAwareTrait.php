<?php
declare(strict_types=1);

namespace BusFactor\Problem;

trait ProblemDetailsAwareTrait
{
    public function getStatus(): int
    {
        return 500;
    }

    public function getType(): string
    {
        return 'about:blank';
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getDetail(): string
    {
        return '';
    }

    public function getAdditionalDetails(): array
    {
        return [];
    }
}

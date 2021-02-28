<?php

declare(strict_types=1);

namespace BusFactor\Problem;

interface ProblemDetailsAwareInterface
{
    public function getStatus(): int;

    public function getType(): string;

    public function getTitle(): string;

    public function getDetail(): string;

    public function getAdditionalDetails(): array;
}

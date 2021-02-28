<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

use BusFactor\Aggregate\EventInterface;

interface StreamEventInterface extends EventInterface
{
    public static function getRevision(): int;

    public function serialize(): array;

    public static function deserialize(array $data): self;
}

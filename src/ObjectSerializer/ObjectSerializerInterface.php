<?php

declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

interface ObjectSerializerInterface
{
    public function serialize(object $object): string;

    public function deserialize(string $payload): object;
}

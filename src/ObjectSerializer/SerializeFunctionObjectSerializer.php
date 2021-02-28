<?php

declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

class SerializeFunctionObjectSerializer implements AdapterInterface
{
    public function serialize(object $object): string
    {
        return serialize($object);
    }

    public function deserialize(string $payload): object
    {
        return unserialize($payload);
    }
}

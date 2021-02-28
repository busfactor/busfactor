<?php

declare(strict_types=1);

namespace BusFactor\ReflectionObjectSerializer;

use BusFactor\ObjectSerializer\AdapterInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;

class ReflectionObjectSerializer implements AdapterInterface
{
    public function serialize(object $object): string
    {
        $reflection = new ReflectionObject($object);
        if ($reflection->getParentClass()) {
            throw new InvalidArgumentException(sprintf('Object %s must not extends.', $reflection->getName()));
        }

        $payload = [
            'c' => $reflection->getName(),
            'p' => [],
        ];
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $payload['p'][$property->getName()] = $value;
        }

        return json_encode($payload);
    }

    public function deserialize(string $payload): object
    {
        $data = json_decode($payload, true);
        $class = $data['c'];

        $object = (new ReflectionClass($class))->newInstanceWithoutConstructor();
        $reflection = new ReflectionObject($object);
        foreach ($data['p'] as $name => $value) {
            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }

        return $object;
    }
}

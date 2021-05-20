<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

use ReflectionClass;
use RuntimeException;

trait SerializationTrait
{
    public function serialize(): array
    {
        return get_object_vars($this);
    }

    public static function deserialize(array $data): StreamEventInterface
    {
        $class = new ReflectionClass(__CLASS__);

        $args = [];
        $constructor = $class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $paramName = $parameter->getName();
                $optional = $parameter->isOptional();
                if (!array_key_exists($paramName, $data) && !$optional) {
                    throw new RuntimeException(sprintf("Deserialization error: No payload value for the constructor argument named '%s'.", $paramName));
                }
                if (array_key_exists($paramName, $data)) {
                    $args[] = $data[$paramName];
                }
            }
        }

        /** @var StreamEventInterface $object */
        $object = $class->newInstanceArgs($args);
        return $object;
    }
}

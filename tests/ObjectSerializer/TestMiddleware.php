<?php
declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

class TestMiddleware implements MiddlewareInterface
{
    /** @var string */
    private $name;

    /** @var Output */
    private $output;

    public function __construct(string $name, Output $output)
    {
        $this->name = $name;
        $this->output = $output;
    }

    public function serialize(object $object, ObjectSerializerInterface $next): string
    {
        $this->output->write('before serialize ' . $this->name);
        $string = $next->serialize($object);
        $this->output->write('after serialize ' . $this->name);
        return $string;
    }

    public function deserialize(string $payload, ObjectSerializerInterface $next): object
    {
        $this->output->write('before unserialize ' . $this->name);
        $object = $next->deserialize($payload);
        $this->output->write('after unserialize ' . $this->name);
        return $object;
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

use PHPUnit\Framework\TestCase;

class SerializeFunctionTest extends TestCase
{
    /** @test */
    public function it_serializes_and_unserializes_objects(): void
    {
        $serializer = new ObjectSerializer(new SerializeFunctionObjectSerializer());

        $object = new TestClass();

        $string = $serializer->serialize($object);
        $newObject = $serializer->deserialize($string);

        $this->assertEquals($object, $newObject);
    }
}

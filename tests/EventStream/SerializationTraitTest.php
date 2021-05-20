<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class SerializationTraitTest extends TestCase
{
    /** @test */
    public function it_serializes_to_array(): void
    {
        $event = new TestEvent('abc', 123, [1, 2, 3]);

        $this->assertSame(
            [
                'string' => 'abc',
                'integer' => 123,
                'array' => [1, 2, 3],
                'bool' => false,
            ],
            $event->serialize()
        );
    }

    /** @test */
    public function it_deserializes_from_array(): void
    {
        /** @var TestEvent $event */
        $event = TestEvent::deserialize(
            [
                'string' => 'abc',
                'integer' => 123,
                'array' => [1, 2, 3],
                'bool' => true,
            ]
        );

        $this->assertSame('abc', $event->getString());
        $this->assertSame(123, $event->getInteger());
        $this->assertSame([1, 2, 3], $event->getArray());
        $this->assertSame(true, $event->getBool());
    }

    /** @test */
    public function it_throws_exception_when_it_deserializes_with_missing_required_attribute(): void
    {
        $this->expectException(RuntimeException::class);

        TestEvent::deserialize(
            [
                'string' => 'abc',
                'array' => [1, 2, 3],
            ]
        );
    }

    /** @test */
    public function it_can_deserialize_with_unordered_and_unused_attributes(): void
    {
        /** @var TestEvent $event */
        $event = TestEvent::deserialize(
            [
                'array' => [1, 2, 3],
                'integer' => 123,
                'string' => 'abc',
                'unused' => 3.1416,
            ]
        );

        $this->assertSame(
            [
                'string' => 'abc',
                'integer' => 123,
                'array' => [1, 2, 3],
                'bool' => false,
            ],
            $event->serialize()
        );
    }
}

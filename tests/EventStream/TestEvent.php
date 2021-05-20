<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

class TestEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    /** @var string */
    private $string;

    /** @var int */
    private $integer;

    /** @var array */
    private $array;

    /** @var bool */
    private $bool;

    public function __construct(string $string, int $integer, array $array, bool $bool = false)
    {
        $this->string = $string;
        $this->integer = $integer;
        $this->array = $array;
        $this->bool = $bool;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getInteger(): int
    {
        return $this->integer;
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function getBool(): bool
    {
        return $this->bool;
    }
}

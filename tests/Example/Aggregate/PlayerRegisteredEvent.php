<?php

declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\EventStream\RevisionTrait;
use BusFactor\EventStream\SerializationTrait;
use BusFactor\EventStream\StreamEventInterface;

class PlayerRegisteredEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    /** @var int */
    private $number;

    /** @var string */
    private $name;

    public function __construct(int $number, string $name)
    {
        $this->number = $number;
        $this->name = $name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

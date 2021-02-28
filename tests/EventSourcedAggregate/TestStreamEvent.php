<?php

declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

use BusFactor\EventStream\RevisionTrait;
use BusFactor\EventStream\SerializationTrait;
use BusFactor\EventStream\StreamEventInterface;

class TestStreamEvent implements StreamEventInterface
{
    use SerializationTrait;
    use RevisionTrait;

    public const REVISION = 1;

    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

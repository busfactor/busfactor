<?php

declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class ProjectionDescriptor
{
    /** @var string */
    private $id;

    /** @var string */
    private $class;

    public function __construct(string $id, string $class)
    {
        $this->id = $id;
        $this->class = $class;
    }

    public static function fromProjection(ProjectionInterface $projection): self
    {
        return new static($projection->getId(), get_class($projection));
    }

    public function getKey(): string
    {
        return sprintf('%s-%s', $this->id, $this->class);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class UnitOfWork
{
    /** @var ProjectionInterface[] */
    private $store = [];

    /** @var ProjectionDescriptor[] */
    private $remove = [];

    public function store(ProjectionInterface $projection): self
    {
        $descriptor = ProjectionDescriptor::fromProjection($projection);
        $clone = clone $this;
        $clone->store[$descriptor->getKey()] = $projection;
        unset($clone->remove[$descriptor->getKey()]);
        return $clone;
    }

    public function remove(string $id, string $class): self
    {
        $descriptor = new ProjectionDescriptor($id, $class);
        $clone = clone $this;
        $clone->remove[$descriptor->getKey()] = $descriptor;
        unset($clone->store[$descriptor->getKey()]);
        return $clone;
    }

    /** @return ProjectionInterface[] */
    public function getStored(): array
    {
        return array_values($this->store);
    }

    public function getOneStored(string $id, string $class): ?ProjectionInterface
    {
        $descriptor = new ProjectionDescriptor($id, $class);
        return $this->store[$descriptor->getKey()] ?? null;
    }

    /** @return ProjectionDescriptor[] */
    public function getRemoved(): array
    {
        return array_values($this->remove);
    }

    public function hasStored(string $id, string $class): bool
    {
        $descriptor = new ProjectionDescriptor($id, $class);
        return isset($this->store[$descriptor->getKey()]);
    }

    public function hasRemoved(string $id, string $class): bool
    {
        $descriptor = new ProjectionDescriptor($id, $class);
        return isset($this->store[$descriptor->getKey()]);
    }
}

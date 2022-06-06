<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

use RuntimeException;

trait AggregateEntityTrait
{
    /** @var bool */
    private $aggregateEntityTrait_isRoot = true;

    /** @var AggregateInterface|null */
    private $aggregateEntityTrait_root;

    /** @var array */
    private $aggregateEntityTrait_childEntities = [];

    public function __attachAggregateRoot($aggregateRoot): void
    {
        if ($this->aggregateEntityTrait_root) {
            throw new RuntimeException('Already attached to an aggregate root.');
        }
        $this->aggregateEntityTrait_isRoot = false;
        $this->aggregateEntityTrait_root = $aggregateRoot;
    }

    private function __handle(RecordedEvent $recordedEvent): void
    {
        $parts = explode('\\', get_class($recordedEvent->getEvent()));
        $method = 'apply' . end($parts);
        if (method_exists($this, $method)) {
            $this->$method($recordedEvent->getEvent(), $recordedEvent);
        }
        foreach ($this->aggregateEntityTrait_childEntities as $entity) {
            ((function () use ($recordedEvent): void {
                $this->__handle($recordedEvent);
            })->bindTo($entity, $entity::class))();
        }
    }

    private function attachChildEntity($entity): void
    {
        /** @var AggregateEntityTrait $entity */
        if (!in_array(AggregateEntityTrait::class, class_uses($entity))) {
            throw new RuntimeException(sprintf(
                'Class %s must use trait %s.',
                $entity::class,
                AggregateEntityTrait::class
            ));
        }
        $aggregateRoot = $this->aggregateEntityTrait_isRoot ? $this : $this->aggregateEntityTrait_root;
        $entity->__attachAggregateRoot($aggregateRoot);
        $this->aggregateEntityTrait_childEntities[] = $entity;
    }

    private function apply(EventInterface $event): void
    {
        if (!$this->aggregateEntityTrait_root) {
            throw new RuntimeException('Entity is not bound to an aggregate root.');
        }
        ((function () use ($event): void {
            $this->apply($event);
        })->bindTo($this->aggregateEntityTrait_root, get_class($this->aggregateEntityTrait_root)))();
    }
}

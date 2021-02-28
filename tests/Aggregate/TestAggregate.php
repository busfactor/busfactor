<?php

declare(strict_types=1);

namespace BusFactor\Aggregate;

class TestAggregate implements AggregateInterface
{
    use AggregateRootTrait {
        AggregateRootTrait::apply as aggregateRootTraitApply;
    }

    /** @var TestAggregateEntity */
    private $entity1;

    /** @var TestAggregateEntity */
    private $entity2;

    public static function getType(): string
    {
        return 'test';
    }

    public static function create(string $id): self
    {
        $me = new static($id);

        $me->entity1 = new TestAggregateEntity();
        $me->attachChildEntity($me->entity1);

        $me->entity2 = new TestAggregateEntity();
        $me->attachChildEntity($me->entity2);

        return $me;
    }

    public function action(): void
    {
        $this->apply(new TestEvent());
    }

    public function getEntity1(): TestAggregateEntity
    {
        return $this->entity1;
    }

    public function getEntity2(): TestAggregateEntity
    {
        return $this->entity2;
    }
}

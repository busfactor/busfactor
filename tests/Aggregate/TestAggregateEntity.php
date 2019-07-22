<?php
declare(strict_types=1);

namespace BusFactor\Aggregate;

class TestAggregateEntity
{
    use AggregateEntityTrait {
        AggregateEntityTrait::apply as aggregateEntityTraitApply;
    }

    /** @var TestEvent[] */
    private $appliedEvents = [];

    public function action(): void
    {
        $this->apply(new TestEvent());
    }

    public function getAppliedEvents(): array
    {
        return $this->appliedEvents;
    }

    private function applyTestEvent(TestEvent $event): void
    {
        $this->appliedEvents[] = $event;
    }
}

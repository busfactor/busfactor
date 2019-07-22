<?php
declare(strict_types=1);

namespace BusFactor\EventSourcedAggregate;

class TestEventSourcedAggregate implements EventSourcedAggregateInterface
{
    use EventSourcedAggregateRootTrait;

    /** @var string|null */
    private $name;

    public static function getType(): string
    {
        return 'test-aggregate';
    }

    public static function create(string $id, string $name): self
    {
        $me = new static($id);
        $me->apply(new TestStreamEvent($name));
        return $me;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if ($this->name !== $name) {
            $this->apply(new TestStreamEvent($name));
        }
    }

    private function applyTestEvent(TestStreamEvent $event): void
    {
        $this->name = $event->getName();
    }
}

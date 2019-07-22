<?php
declare(strict_types=1);

namespace BusFactor\Aggregate;

trait AggregateRootTrait
{
    use AggregateEntityTrait;

    /** @var string */
    private $aggregateRootTrait_aggregateId;

    /** @var int */
    private $aggregateRootTrait_version = 0;

    /** @var RecordedEvent[] */
    private $aggregateRootTrait_newEvents = [];

    public function __construct(string $aggregateId)
    {
        $this->aggregateRootTrait_aggregateId = $aggregateId;
    }

    public function getAggregateId(): string
    {
        return $this->aggregateRootTrait_aggregateId;
    }

    public function getVersion(): int
    {
        return $this->aggregateRootTrait_version;
    }

    public function pullNewEvents(): array
    {
        $recordedEvents = $this->peekNewEvents();
        $this->aggregateRootTrait_newEvents = [];
        return $recordedEvents;
    }

    public function peekNewEvents(): array
    {
        return $this->aggregateRootTrait_newEvents;
    }

    private function apply(EventInterface $event): void
    {
        $this->aggregateRootTrait_version++;
        $recordedEvent = new RecordedEvent($event, $this->aggregateRootTrait_version);
        $this->__handle($recordedEvent);
        $this->aggregateRootTrait_newEvents[] = $recordedEvent;
    }
}

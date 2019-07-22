<?php
declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\CommandBus\CommandBusInterface;
use BusFactor\EventBus\EventBus;
use BusFactor\EventStore\EventStore;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\ProjectionStore\ProjectionStore;

class Scenario
{
    /** @var EventBus */
    private $eventBus;

    /** @var CommandBusInterface */
    private $commandBus;

    /** @var EventStoreInterface */
    private $eventStore;

    /** @var EventBusTraceMiddleware */
    private $eventBusTrace;

    /** @var ProjectionStoreTraceMiddleware */
    private $projectionStoreTrace;

    public function __construct(EventBus $eventBus, CommandBusInterface $commandBus, ProjectionStore $projectionStore)
    {
        $this->eventBus = $eventBus;
        $this->commandBus = $commandBus;
        $this->eventStore = new EventStore(new InMemoryEventStoreAdapter());
        $this->eventBusTrace = new EventBusTraceMiddleware();
        $this->eventBus->addMiddleware($this->eventBusTrace);
        $this->projectionStoreTrace = new ProjectionStoreTraceMiddleware();
        $projectionStore->addMiddleware($this->projectionStoreTrace);
    }

    public function play(Play ...$plays): void
    {
        foreach ($plays as $play) {
            $play->run(
                $this->eventBus,
                $this->eventBusTrace,
                $this->eventStore,
                $this->commandBus,
                $this->projectionStoreTrace
            );
        }
    }
}

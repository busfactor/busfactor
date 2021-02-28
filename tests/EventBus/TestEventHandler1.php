<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\StreamEventInterface;

class TestEventHandler1 implements EventHandlerInterface
{
    use EventHandlerTrait;

    /** @var StreamEventInterface[] */
    private $handledEvents = [];

    public static function getSubscribedEventClasses(): array
    {
        return [
            TestEvent1::class,
        ];
    }

    /** @return StreamEventInterface[] */
    public function getHandledEvents(): array
    {
        return $this->handledEvents;
    }

    private function handleTestEvent1(string $aggregateId, TestEvent1 $event, Envelope $envelope): void
    {
        $this->handledEvents[] = $event;
    }
}

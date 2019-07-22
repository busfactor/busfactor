<?php
declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\StreamEventInterface;

class TestEventHandler2 implements EventHandlerInterface
{
    use EventHandlerTrait;

    /** @var StreamEventInterface[] */
    private $handledEvents = [];

    public static function getSubscribedEventClasses(): array
    {
        return [
            TestEvent2::class,
        ];
    }

    /** @return StreamEventInterface[] */
    public function getHandledEvents(): array
    {
        return $this->handledEvents;
    }

    private function handleTestEvent2(string $aggregateId, TestEvent2 $event, Envelope $envelope): void
    {
        $this->handledEvents[] = $event;
    }
}

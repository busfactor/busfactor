<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;

class Dispatcher implements EventStreamPublisherInterface
{
    /** @var EventHandlerInterface[][] */
    private $subscribers = [];

    public function publish(Stream $stream): void
    {
        $envelopes = $stream->getEnvelopes();
        foreach ($envelopes as $envelope) {
            $this->notifySubscribers($stream->getStreamId(), $envelope);
        }
    }

    public function subscribe(string $eventClass, EventHandlerInterface $subscriber): void
    {
        if (!isset($this->subscribers[$eventClass])) {
            $this->subscribers[$eventClass] = [];
        }
        $this->subscribers[$eventClass][] = $subscriber;
    }

    private function notifySubscribers(string $aggregateId, Envelope $envelope): void
    {
        $subscribers = $this->resolveSubscribers($envelope);
        foreach ($subscribers as $subscriber) {
            $subscriber->handle($aggregateId, $envelope);
        }
    }

    /** @return EventHandlerInterface[] */
    private function resolveSubscribers(Envelope $envelope): array
    {
        $name = get_class($envelope->getEvent());
        if (!isset($this->subscribers[$name])) {
            return [];
        }
        $subscribers = [];
        foreach ($this->subscribers[$name] as $subscriber) {
            $subscribers[] = $subscriber;
        }
        return $subscribers;
    }
}

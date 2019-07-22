<?php
declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;
use RuntimeException;

trait EventHandlerTrait
{
    public function handle(string $aggregateId, Envelope $envelope): void
    {
        $event = $envelope->getEvent();

        $classParts = explode('\\', get_class($event));
        $method = 'handle' . end($classParts);

        if (method_exists($this, $method)) {
            $this->$method($aggregateId, $event, $envelope);
        } else {
            throw new RuntimeException(sprintf(
                'Function "%s" must be implemented in class %s',
                $method,
                get_class($this)
            ));
        }
    }
}

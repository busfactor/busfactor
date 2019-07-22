<?php
declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Envelope;

interface EventHandlerInterface
{
    public function handle(string $aggregateId, Envelope $envelope): void;
}

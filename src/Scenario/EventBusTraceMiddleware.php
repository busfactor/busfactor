<?php
declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\EventBus\EventStreamPublisherInterface;
use BusFactor\EventBus\MiddlewareInterface;
use BusFactor\EventStream\Stream;

class EventBusTraceMiddleware implements MiddlewareInterface
{
    /** @var Stream[] */
    private $traceStack = [];

    /** @var bool */
    private $tracing = false;

    public function publish(Stream $stream, EventStreamPublisherInterface $next): void
    {
        $next->publish($stream);
        if ($this->tracing) {
            $this->traceStack[] = $stream;
        }
    }

    public function startTracing(): void
    {
        if ($this->tracing) {
            return;
        }
        $this->tracing = true;
        $this->traceStack = [];
    }

    public function stopTracing(): void
    {
        $this->tracing = false;
    }

    public function clearTrace(): void
    {
        $this->traceStack = [];
    }

    public function isTracing(): bool
    {
        return $this->tracing;
    }

    /** @return Stream[] */
    public function getTracedEventStreams(): array
    {
        return $this->traceStack;
    }
}

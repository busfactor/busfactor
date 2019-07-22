<?php
declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Stream;

class EventStreamPublisherDelegator implements EventStreamPublisherInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var EventStreamPublisherInterface|null */
    private $next;

    public function __construct(MiddlewareInterface $middleware, ?EventStreamPublisherInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function publish(Stream $stream): void
    {
        $this->middleware->publish($stream, $this->next);
    }
}

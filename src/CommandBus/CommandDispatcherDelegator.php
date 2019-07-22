<?php
declare(strict_types=1);

namespace BusFactor\CommandBus;

class CommandDispatcherDelegator implements CommandDispatcherInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var CommandDispatcherInterface|null */
    private $next;

    public function __construct(MiddlewareInterface $middleware, ?CommandDispatcherInterface $next = null)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function dispatch(CommandInterface $command): void
    {
        $this->middleware->dispatch($command, $this->next);
    }
}

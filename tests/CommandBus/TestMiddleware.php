<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

class TestMiddleware implements MiddlewareInterface
{
    /** @var string */
    private $name;

    /** @var array */
    private $output;

    public function __construct(string $name, array &$output)
    {
        $this->name = $name;
        $this->output = &$output;
    }

    public function dispatch(CommandInterface $command, CommandDispatcherInterface $next): void
    {
        $this->output[] = 'before ' . $this->name;
        $next->dispatch($command);
        $this->output[] = 'after ' . $this->name;
    }
}

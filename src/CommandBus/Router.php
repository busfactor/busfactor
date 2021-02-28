<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

class Router implements CommandDispatcherInterface
{
    /** @var CommandHandlerInterface[] */
    private $map;

    public function dispatch(CommandInterface $command): void
    {
        if (is_object($command)) {
            $this->route($command);
        }
    }

    public function registerHandler(string $commandClass, CommandHandlerInterface $handler): void
    {
        $this->map[$commandClass] = $handler;
    }

    private function route(CommandInterface $command): void
    {
        $name = get_class($command);
        if (isset($this->map[$name])) {
            $this->map[$name]->handle($command);
        }
    }
}

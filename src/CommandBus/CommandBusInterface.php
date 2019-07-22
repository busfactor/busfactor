<?php
declare(strict_types=1);

namespace BusFactor\CommandBus;

interface CommandBusInterface extends CommandDispatcherInterface
{
    public function registerHandler(string $commandClass, CommandHandlerInterface $handler): void;
}

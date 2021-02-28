<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

interface CommandDispatcherInterface
{
    public function dispatch(CommandInterface $command): void;
}

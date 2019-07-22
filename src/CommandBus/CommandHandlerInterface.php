<?php
declare(strict_types=1);

namespace BusFactor\CommandBus;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}

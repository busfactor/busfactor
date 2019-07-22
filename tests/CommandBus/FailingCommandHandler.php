<?php
declare(strict_types=1);

namespace BusFactor\CommandBus;

class FailingCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    public static function getHandledCommandClasses(): array
    {
        return [];
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\CommandBus;

class SuccessfulCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    public static function getHandledCommandClasses(): array
    {
        return [
            TestCommand::class,
        ];
    }

    private function handleTestCommand(TestCommand $command): void
    {
    }
}

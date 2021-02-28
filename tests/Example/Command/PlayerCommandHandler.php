<?php

declare(strict_types=1);

namespace BusFactor\Example\Command;

use BusFactor\CommandBus\CommandHandlerInterface;
use BusFactor\CommandBus\CommandHandlerTrait;
use BusFactor\Example\Aggregate\Player;
use BusFactor\Example\Aggregate\PlayerRepository;

class PlayerCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    /** @var PlayerRepository */
    private $players;

    public function __construct(PlayerRepository $players)
    {
        $this->players = $players;
    }

    public static function getHandledCommandClasses(): array
    {
        return [
            RegisterPlayerCommand::class,
            ChangePlayerNameCommand::class,
        ];
    }

    private function handleRegisterPlayerCommand(RegisterPlayerCommand $command): void
    {
        $player = Player::register($command->getId(), $command->getNumber(), $command->getName());
        $this->players->store($player);
    }

    private function handleChangePlayerNameCommand(ChangePlayerNameCommand $command): void
    {
        $player = $this->players->find($command->getId());
        $player->changeName($command->getName());
        $this->players->store($player);
    }
}

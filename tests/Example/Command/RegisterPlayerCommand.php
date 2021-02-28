<?php

declare(strict_types=1);

namespace BusFactor\Example\Command;

use BusFactor\CommandBus\CommandInterface;

class RegisterPlayerCommand implements CommandInterface
{
    /** @var string */
    private $id;

    /** @var int */
    private $number;

    /** @var string */
    private $name;

    public function __construct(string $id, int $number, string $name)
    {
        $this->id = $id;
        $this->number = $number;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

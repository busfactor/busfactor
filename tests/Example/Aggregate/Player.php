<?php
declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\EventSourcedAggregate\EventSourcedAggregateInterface;

class Player implements EventSourcedAggregateInterface
{
    use \BusFactor\EventSourcedAggregate\EventSourcedAggregateRootTrait;

    /** @var int */
    private $number;

    /** @var string */
    private $name;

    /** @var int */
    private $points;

    public static function getType(): string
    {
        return 'player';
    }

    public static function register(string $id, int $number, string $name): self
    {
        $me = new static($id);
        $me->apply(new PlayerRegisteredEvent($number, $name));
        return $me;
    }

    public function changeName(string $name): void
    {
        if ($name !== $this->name) {
            $this->apply(new PlayerNameChangedEvent($this->name, $name));
        }
    }

    private function applyPlayerRegisteredEvent(PlayerRegisteredEvent $event): void
    {
        $this->number = $event->getNumber();
        $this->name = $event->getName();
        $this->points = 0;
    }

    private function applyPlayerNameChangedEvent(PlayerNameChangedEvent $event): void
    {
        $this->name = $event->getNew();
    }
}

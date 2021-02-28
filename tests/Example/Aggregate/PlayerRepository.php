<?php

declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\AggregateStore\AggregateStore;
use BusFactor\AggregateStore\AggregateStoreInterface;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventSourcedAggregateStore\EventSourcedAggregateFactory;
use BusFactor\EventSourcedAggregateStore\EventSourcedAggregateStoreAdapter;
use BusFactor\EventStore\EventStoreInterface;

class PlayerRepository
{
    /** @var AggregateStoreInterface */
    private $store;

    public function __construct(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $this->store = new AggregateStore(
            new EventSourcedAggregateStoreAdapter(
                new EventSourcedAggregateFactory(Player::class),
                $eventStore,
                $eventBus
            )
        );
    }

    public function find(string $playerId): Player
    {
        /** @var Player $player */
        $player = $this->store->find($playerId, Player::getType());
        return $player;
    }

    public function exists(string $playerId): bool
    {
        return $this->store->has($playerId, Player::getType());
    }

    public function store(Player $player): void
    {
        $this->store->store($player);
    }
}

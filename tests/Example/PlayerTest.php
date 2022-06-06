<?php

declare(strict_types=1);

namespace BusFactor\Example;

use BusFactor\CommandBus\CommandBus;
use BusFactor\EventBus\EventBus;
use BusFactor\EventStore\EventStore;
use BusFactor\EventStore\InMemoryEventStoreAdapter;
use BusFactor\Example\Aggregate\PlayerRegisteredEvent;
use BusFactor\Example\Aggregate\PlayerRepository;
use BusFactor\Example\Command\PlayerCommandHandler;
use BusFactor\Example\Command\RegisterPlayerCommand;
use BusFactor\Example\Projection\PlayerListProjection;
use BusFactor\Example\Projection\PlayerListProjector;
use BusFactor\ProjectionStore\InMemoryProjectionStoreAdapter;
use BusFactor\ProjectionStore\ProjectionStore;
use BusFactor\Scenario\AssertionsTrait;
use BusFactor\Scenario\Play;
use BusFactor\Scenario\PublishedStreams;
use BusFactor\Scenario\Scenario;
use BusFactor\Scenario\UpdatedProjections;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    use AssertionsTrait;

    /** @var Scenario */
    private $scenario;

    public function setUp(): void
    {
        parent::setUp();

        $eventBus = new EventBus();
        $commandBus = new CommandBus();
        $eventStore = new EventStore(new InMemoryEventStoreAdapter());
        $projectionStore = new ProjectionStore(new InMemoryProjectionStoreAdapter());
        $playerRepository = new PlayerRepository($eventStore, $eventBus);
        $playerCommandHandler = new PlayerCommandHandler($playerRepository);
        $playerListProjector = new PlayerListProjector($projectionStore);

        foreach ($playerCommandHandler::getHandledCommandClasses() as $commandClass) {
            $commandBus->registerHandler($commandClass, $playerCommandHandler);
        }
        foreach ($playerListProjector::getSubscribedEventClasses() as $eventClass) {
            $eventBus->subscribe($eventClass, $playerListProjector);
        }

        $this->scenario = new Scenario($eventBus, $commandBus, $projectionStore);
    }

    public function testPlayerRegistration(): void
    {
        $play = (new Play())
            ->dispatch(
                new RegisterPlayerCommand('123', 1234, 'John Smith')
            )
            ->testEvents(function (PublishedStreams $streams): void {
                $this->assertPublishedStreamsContainExactly([PlayerRegisteredEvent::class => 1], $streams);
            })
            ->testProjections(function (UpdatedProjections $projections): void {
                $this->assertUpdatedProjectionsContainExactly([PlayerListProjection::class => 1], $projections);

                /** @var PlayerListProjection $projection */
                $projection = $projections->getAllOf(PlayerListProjection::class)[0];
                $this->assertCount(1, $projection->getPayload());
                $this->assertEquals([
                    '123' => [
                        'number' => 1234,
                        'name' => 'John Smith',
                    ],
                ], $projection->getPayload());
            });

        $this->scenario->play($play);
    }
}

<?php
declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\CommandBus\CommandBusInterface;
use BusFactor\CommandBus\CommandInterface;
use BusFactor\EventBus\EventBusInterface;
use BusFactor\EventStore\EventStoreInterface;
use BusFactor\EventStream\Stream;
use Exception;
use InvalidArgumentException;

class Play
{
    /** @var Stream[] */
    private $initialStreams = [];

    /** @var CommandInterface[] */
    private $initialCommands = [];

    /** @var CommandInterface[] */
    private $commands = [];

    /** @var callable[] */
    private $actions = [];

    /** @var callable[] */
    private $eventsAssertions = [];

    /** @var callable[] */
    private $projectionsAssertions = [];

    /** @var callable[] */
    private $customAssertions = [];

    /** @var string|null */
    private $expectedException;

    public function withInitialEvents(...$streams): self
    {
        $clone = clone $this;
        foreach ($streams as $stream) {
            if (!($stream instanceof Stream) && !is_callable($stream)) {
                throw new InvalidArgumentException();
            }
            $clone->initialStreams[] = $stream;
        }
        return $clone;
    }

    public function withInitialCommands(...$commands): self
    {
        $clone = clone $this;
        foreach ($commands as $command) {
            if (!($command instanceof CommandInterface) && !is_callable($command)) {
                throw new InvalidArgumentException();
            }
            $clone->initialCommands[] = $command;
        }
        return $clone;
    }

    public function doAction(callable $action): self
    {
        $clone = clone $this;
        $clone->actions[] = $action;
        return $clone;
    }

    public function dispatch(...$commands): self
    {
        $clone = clone $this;
        foreach ($commands as $command) {
            if (!($command instanceof CommandInterface) && !is_callable($command)) {
                throw new InvalidArgumentException();
            }
            $clone->commands[] = $command;
        }
        return $clone;
    }

    public function testEvents(callable $assertion): self
    {
        $clone = clone $this;
        $clone->eventsAssertions[] = $assertion;
        return $clone;
    }

    public function testProjections(callable $assertion): self
    {
        $clone = clone $this;
        $clone->projectionsAssertions[] = $assertion;
        return $clone;
    }

    public function testThat(callable $assertion): self
    {
        $clone = clone $this;
        $clone->customAssertions[] = $assertion;
        return $clone;
    }

    public function expectException(string $exceptionClass): self
    {
        $clone = clone $this;
        $clone->expectedException = $exceptionClass;
        return $clone;
    }

    public function run(
        EventBusInterface $eventBus,
        EventBusTraceMiddleware $eventBusTrace,
        EventStoreInterface $eventStore,
        CommandBusInterface $commandBus,
        ProjectionStoreTraceMiddleware $projectionTrace
    ): void {
        $catchedExceptions = [];
        $expectedExceptionThrown = false;

        $eventBusTrace->stopTracing();
        $projectionTrace->stopTracing();

        foreach ($this->initialStreams as $stream) {
            $eventStore->append($this->evaluate($stream));
            $eventBus->publish($this->evaluate($stream));
        }
        foreach ($this->initialCommands as $command) {
            $commandBus->dispatch($this->evaluate($command));
        }

        $eventBusTrace->startTracing();
        $projectionTrace->startTracing();

        /* Commands and actions */
        foreach ($this->commands as $command) {
            try {
                $commandBus->dispatch($this->evaluate($command));
            } catch (Exception $e) {
                if ($this->expectedException && ($e instanceof $this->expectedException)) {
                    $expectedExceptionThrown = true;
                } else {
                    throw $e;
                }
            }
        }
        foreach ($this->actions as $action) {
            try {
                $action();
            } catch (Exception $e) {
                if ($this->expectedException && ($e instanceof $this->expectedException)) {
                    $expectedExceptionThrown = true;
                } else {
                    throw $e;
                }
            }
        }

        /* Assert expected exception was thrown */
        if ($this->expectedException && !$expectedExceptionThrown) {
            throw new ExpectedExceptionWasNotThrownException($this->expectedException);
        }

        /* Assertions */
        $publishedStreams = $eventBusTrace->getTracedEventStreams();
        $eventBusTrace->clearTrace();
        foreach ($this->eventsAssertions as $assertion) {
            $assertion(new PublishedStreams($publishedStreams));
        }
        $updatedProjections = $projectionTrace->getTracedProjections();
        $projectionTrace->clearTrace();
        foreach ($this->projectionsAssertions as $assertion) {
            $assertion(new UpdatedProjections($updatedProjections));
        }
        foreach ($this->customAssertions as $assertion) {
            $assertion();
        }
    }

    private function evaluate($value)
    {
        return is_callable($value) ? $value() : $value;
    }
}

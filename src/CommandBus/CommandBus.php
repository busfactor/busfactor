<?php

declare(strict_types=1);

namespace BusFactor\CommandBus;

class CommandBus implements CommandBusInterface
{
    /** @var Router */
    private $router;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var CommandDispatcherInterface|null */
    private $chain = null;

    public function __construct()
    {
        $this->router = new Router();
        $this->middlewares = [];
        $this->chain = null;
    }

    public function dispatch(CommandInterface $command): void
    {
        if (!$this->chain) {
            $this->chainMiddlewares();
        }
        $this->chain->dispatch($command);
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
        $this->chain = null;
    }

    /** @return MiddlewareInterface[] */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function registerHandler(string $commandClass, CommandHandlerInterface $handler): void
    {
        $this->router->registerHandler($commandClass, $handler);
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (CommandDispatcherInterface $carry, MiddlewareInterface $item): CommandDispatcherInterface {
                return new CommandDispatcherDelegator($item, $carry);
            },
            $this->router
        );
    }
}

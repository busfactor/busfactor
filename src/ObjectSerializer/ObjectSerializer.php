<?php
declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

class ObjectSerializer implements ObjectSerializerInterface
{
    /** @var AdapterInterface */
    private $adapter;

    /** @var MiddlewareInterface[] */
    private $middlewares = [];

    /** @var ObjectSerializerInterface|null */
    private $chain;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->middlewares = [];
        $this->chainMiddlewares();
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
        $this->chainMiddlewares();
    }

    /** @return MiddlewareInterface[] */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function serialize(object $object): string
    {
        return $this->chain->serialize($object);
    }

    public function deserialize(string $payload): object
    {
        return $this->chain->deserialize($payload);
    }

    private function chainMiddlewares(): void
    {
        $this->chain = array_reduce(
            $this->middlewares,
            function (ObjectSerializerInterface $carry, MiddlewareInterface $item): ObjectSerializerInterface {
                return new ObjectSerializerDelegator($item, $carry);
            },
            $this->adapter
        );
    }
}

<?php

declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

class ObjectSerializerDelegator implements ObjectSerializerInterface
{
    /** @var MiddlewareInterface */
    private $middleware;

    /** @var ObjectSerializerInterface|null */
    private $next;

    public function __construct(MiddlewareInterface $middleware, ?ObjectSerializerInterface $next)
    {
        $this->middleware = $middleware;
        $this->next = $next;
    }

    public function serialize(object $object): string
    {
        return $this->middleware->serialize($object, $this->next);
    }

    public function deserialize(string $payload): object
    {
        return $this->middleware->deserialize($payload, $this->next);
    }
}

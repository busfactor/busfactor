<?php

declare(strict_types=1);

namespace BusFactor\EventBus;

use BusFactor\EventStream\Stream;

class TestMiddleware implements MiddlewareInterface
{
    /** @var string */
    private $name;

    /** @var array */
    private $output;

    public function __construct(string $name, array &$output)
    {
        $this->name = $name;
        $this->output = &$output;
    }

    public function publish(Stream $stream, EventStreamPublisherInterface $next): void
    {
        $this->output[] = 'before ' . $this->name;
        $next->publish($stream);
        $this->output[] = 'after ' . $this->name;
    }
}

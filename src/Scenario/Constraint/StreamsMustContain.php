<?php

declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\PublishedStreams;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class StreamsMustContain extends Constraint
{
    /** @var string */
    private $eventClass;

    public function __construct(string $eventClass)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->eventClass = $eventClass;
    }

    public function toString(): string
    {
        return " contain instance(s) of {$this->eventClass}";
    }

    /**
     * @param PublishedStreams $publishedStreams
     */
    public function matches($publishedStreams): bool
    {
        foreach ($publishedStreams->getAll() as $stream) {
            foreach ($stream->getEnvelopes() as $envelope) {
                if (get_class($envelope->getEvent()) === $this->eventClass) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function failureDescription($other): string
    {
        return 'published streams' . $this->toString();
    }
}

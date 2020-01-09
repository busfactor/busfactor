<?php
declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\PublishedStreams;
use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;

class StreamsMustContainExactly extends Constraint
{
    /** @var int */
    private $count;

    /** @var string */
    private $eventClass;

    /** @var int */
    private $found;

    public function __construct(int $count, string $eventClass)
    {
        if ((new ReflectionClass(Constraint::class))->hasMethod('__construct')) {
            parent::__construct();
        }
        $this->count = $count;
        $this->eventClass = $eventClass;
        $this->found = 0;
    }

    public function toString(): string
    {
        return " contain exactly {$this->count} instance(s) of {$this->eventClass}, found {$this->found}";
    }

    /**
     * @param PublishedStreams $publishedStreams
     */
    public function matches($publishedStreams): bool
    {
        $this->found = 0;
        foreach ($publishedStreams->getAll() as $stream) {
            foreach ($stream->getEnvelopes() as $envelope) {
                if (get_class($envelope->getEvent()) === $this->eventClass) {
                    $this->found++;
                }
            }
        }
        return $this->found === $this->count;
    }

    protected function failureDescription($other): string
    {
        return 'published streams' . $this->toString();
    }
}

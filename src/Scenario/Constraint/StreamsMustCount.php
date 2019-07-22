<?php
declare(strict_types=1);

namespace BusFactor\Scenario\Constraint;

use BusFactor\Scenario\PublishedStreams;
use PHPUnit\Framework\Constraint\Constraint;

class StreamsMustCount extends Constraint
{
    /** @var int */
    private $count;

    public function __construct(int $count)
    {
        parent::__construct();
        $this->count = $count;
    }

    public function toString(): string
    {
        return " have {$this->count} stream(s)";
    }

    /**
     * @param PublishedStreams $publishedStreams
     */
    public function matches($publishedStreams): bool
    {
        $count = 0;
        foreach ($publishedStreams->getAll() as $stream) {
            $count += count($stream->getEnvelopes());
        }
        return $count === $this->count;
    }

    protected function failureDescription($other): string
    {
        return 'published streams' . $this->toString();
    }
}

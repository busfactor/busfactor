<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;
use Countable;

class PublishedStreams implements Countable
{
    /** @var Stream[] */
    private $streams;

    public function __construct(array $streams)
    {
        $this->streams = $streams;
    }

    public function count(): int
    {
        return count($this->streams);
    }

    /** @return Stream[] */
    public function getAll(): array
    {
        return $this->streams;
    }

    /** @return Envelope[] */
    public function getAllOf(string $eventClass): array
    {
        $envelopes = [];
        foreach ($this->streams as $stream) {
            foreach ($stream->getEnvelopes() as $envelope) {
                if (get_class($envelope->getEvent()) === $eventClass) {
                    $envelopes[] = $envelope;
                }
            }
        }
        return $envelopes;
    }
}

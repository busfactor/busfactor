<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

class Stream
{
    /** @var string */
    private $streamId;

    /** @var string */
    private $streamType;

    /** @var Envelope[] */
    private $envelopes = [];

    /** @var int */
    private $lowestVersion = 0;

    /** @var int */
    private $highestVersion = 0;

    public function __construct(string $streamId, string $streamType)
    {
        $this->streamId = $streamId;
        $this->streamType = $streamType;
    }

    public function getStreamId(): string
    {
        return $this->streamId;
    }

    public function getStreamType(): string
    {
        return $this->streamType;
    }

    public function withEnvelope(Envelope $envelope): self
    {
        $clone = clone $this;
        $clone->envelopes[] = $envelope;
        usort($clone->envelopes, fn (Envelope $a, Envelope $b) => $a->getVersion() <=> $b->getVersion());

        if ($clone->highestVersion == 0) {
            $clone->highestVersion = $envelope->getVersion();
        } elseif ($envelope->getVersion() > $clone->highestVersion) {
            $clone->highestVersion = $envelope->getVersion();
        }
        if ($clone->lowestVersion == 0) {
            $clone->lowestVersion = $envelope->getVersion();
        } elseif ($envelope->getVersion() < $clone->lowestVersion) {
            $clone->lowestVersion = $envelope->getVersion();
        }

        return $clone;
    }

    /** @return Envelope[] */
    public function getEnvelopes(): array
    {
        return $this->envelopes;
    }

    public function getLowestVersion(): int
    {
        return $this->lowestVersion;
    }

    public function getHighestVersion(): int
    {
        return $this->highestVersion;
    }
}

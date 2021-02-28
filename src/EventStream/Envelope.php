<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

use BusFactor\Aggregate\RecordedEvent;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class Envelope
{
    /** @var StreamEventInterface */
    private $event;

    /** @var Metadata */
    private $metadata;

    /** @var int */
    private $version;

    /** @var DateTimeInterface */
    private $recordTime;

    private function __construct(
        StreamEventInterface $event,
        Metadata $metadata,
        int $version,
        DateTimeInterface $recordTime
    ) {
        $this->event = $event;
        $this->metadata = $metadata;
        $this->version = $version;
        $this->recordTime = clone $recordTime;
    }

    public static function fromRecordedEvent(RecordedEvent $recordedEvent): self
    {
        if (!$recordedEvent->getEvent() instanceof StreamEventInterface) {
            throw new InvalidArgumentException('Class must implement ' . StreamEventInterface::class);
        }
        /** @var StreamEventInterface $event */
        $event = $recordedEvent->getEvent();
        return new static(
            $event,
            new Metadata(),
            $recordedEvent->getVersion(),
            $recordedEvent->getRecordTime()
        );
    }

    public static function create(
        StreamEventInterface $event,
        Metadata $metadata,
        int $version,
        DateTimeImmutable $recordTime
    ): self {
        return new static($event, $metadata, $version, $recordTime);
    }

    public static function createNow(StreamEventInterface $event, Metadata $metadata, int $version): self
    {
        return new static($event, $metadata, $version, new DateTimeImmutable());
    }

    public function getEvent(): StreamEventInterface
    {
        return $this->event;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getRecordTime(): DateTimeInterface
    {
        return clone $this->recordTime;
    }

    public function withMetadata(Metadata $metadata): self
    {
        $clone = clone $this;
        $clone->metadata = $metadata;
        return $clone;
    }
}

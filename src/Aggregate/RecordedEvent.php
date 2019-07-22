<?php
declare(strict_types=1);

namespace BusFactor\Aggregate;

use DateTimeImmutable;
use DateTimeInterface;

class RecordedEvent
{
    /** @var EventInterface */
    private $event;

    /** @var int */
    private $version;

    /** @var DateTimeInterface */
    private $recordTime;

    public function __construct(EventInterface $event, int $version)
    {
        $this->event = $event;
        $this->version = $version;
        $this->recordTime = new DateTimeImmutable();
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getRecordTime(): DateTimeInterface
    {
        return $this->recordTime;
    }

    public function withRecordTime(DateTimeInterface $recordTime): self
    {
        $clone = clone $this;
        $clone->recordTime = $recordTime;
        return $clone;
    }
}

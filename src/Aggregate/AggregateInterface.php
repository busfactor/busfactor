<?php
declare(strict_types=1);

namespace BusFactor\Aggregate;

interface AggregateInterface
{
    public static function getType(): string;

    public function getAggregateId(): string;

    public function getVersion(): int;

    /** @return RecordedEvent[] */
    public function peekNewEvents(): array;

    /** @return RecordedEvent[] */
    public function pullNewEvents(): array;
}

<?php
declare(strict_types=1);

namespace BusFactor\EventStore;

class StreamNotFoundException extends EventStoreException
{
    public static function forId(string $streamId, string $streamType): self
    {
        $message = sprintf('Stream of type [%s] and ID [%s] not found.', $streamType, $streamId);

        return new static($message);
    }

    public function getType(): string
    {
        return 'stream-not-found';
    }

    public function getTitle(): string
    {
        return 'stream-not-found';
    }
}

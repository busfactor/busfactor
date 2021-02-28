<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricherEventBusMiddleware;

use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Stream;
use BusFactor\StreamEnricher\StreamEnricherInterface;

class TestEnricher implements StreamEnricherInterface
{
    public function enrich(Stream $stream): Stream
    {
        return array_reduce($stream->getEnvelopes(), function (Stream $enrichedStream, Envelope $envelope) {
            $metadata = $envelope->getMetadata()
                ->with('foo', 'bar');
            return $enrichedStream->withEnvelope($envelope->withMetadata($metadata));
        }, new Stream($stream->getStreamId(), $stream->getStreamType()));
    }
}

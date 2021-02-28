<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricher;

use BusFactor\EventStream\Stream;

interface StreamEnricherInterface
{
    public function enrich(Stream $stream): Stream;
}

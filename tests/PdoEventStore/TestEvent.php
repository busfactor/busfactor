<?php

declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use BusFactor\EventStream\RevisionTrait;
use BusFactor\EventStream\SerializationTrait;
use BusFactor\EventStream\StreamEventInterface;

class TestEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;
}

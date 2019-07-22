<?php
declare(strict_types=1);

namespace BusFactor\StreamPublishingInspection;

use BusFactor\EventStream\RevisionTrait;
use BusFactor\EventStream\SerializationTrait;
use BusFactor\EventStream\StreamEventInterface;

class TestEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;
}

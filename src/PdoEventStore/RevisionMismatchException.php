<?php
declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use BusFactor\EventStore\EventStoreException;

class RevisionMismatchException extends EventStoreException
{
}

<?php
declare(strict_types=1);

namespace BusFactor\Example\Aggregate;

use BusFactor\EventStream\RevisionTrait;
use BusFactor\EventStream\SerializationTrait;
use BusFactor\EventStream\StreamEventInterface;

class PlayerNameChangedEvent implements StreamEventInterface
{
    use RevisionTrait;
    use SerializationTrait;

    public const REVISION = 1;

    /** @var string */
    private $previous;

    /** @var string */
    private $new;

    public function __construct(string $previous, string $new)
    {
        $this->previous = $previous;
        $this->new = $new;
    }

    public function getPrevious(): string
    {
        return $this->previous;
    }

    public function getNew(): string
    {
        return $this->new;
    }
}

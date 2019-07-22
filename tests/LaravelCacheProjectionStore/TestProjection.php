<?php
declare(strict_types=1);

namespace BusFactor\LaravelCacheProjectionStore;

use BusFactor\Projection\ProjectionInterface;

class TestProjection implements ProjectionInterface
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

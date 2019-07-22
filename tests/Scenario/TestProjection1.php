<?php
declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\Projection\ProjectionInterface;

class TestProjection1 implements ProjectionInterface
{
    public function getId(): string
    {
        return 'projection1';
    }
}

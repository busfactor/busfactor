<?php
declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use PHPUnit\Framework\TestCase;

class UnitOfWorkTest extends TestCase
{
    /** @test */
    public function it_tracks_stored_and_removed_projection(): void
    {
        $unit = new UnitOfWork();
        $this->assertEmpty($unit->getStored());
        $this->assertEmpty($unit->getRemoved());

        $unit = $unit->store(new TestProjection('123'));
        $this->assertCount(1, $unit->getStored());
        $this->assertEmpty($unit->getRemoved());

        $unit = $unit->remove('123', TestProjection::class);
        $this->assertEmpty($unit->getStored());
        $this->assertCount(1, $unit->getRemoved());

        $unit = $unit->remove('123', TestProjection::class);
        $this->assertEmpty($unit->getStored());
        $this->assertCount(1, $unit->getRemoved());

        $unit = $unit->store(new TestProjection('123'));
        $this->assertCount(1, $unit->getStored());
        $this->assertEmpty($unit->getRemoved());
    }
}
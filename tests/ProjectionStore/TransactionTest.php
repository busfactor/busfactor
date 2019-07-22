<?php
declare(strict_types=1);

namespace BusFactor\ProjectionStore;

use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_commits_and_rollbacks(): void
    {
        $store = new ProjectionStore(new InMemoryProjectionStoreAdapter());
        $this->assertFalse($store->has('123', TestProjection::class));

        $store->store(new TestProjection('123'));
        $this->assertTrue($store->has('123', TestProjection::class));

        $store->rollback();
        $this->assertFalse($store->has('123', TestProjection::class));

        $store->store(new TestProjection('123'));
        $this->assertTrue($store->has('123', TestProjection::class));

        $store->commit();
        $this->assertTrue($store->has('123', TestProjection::class));
    }
}

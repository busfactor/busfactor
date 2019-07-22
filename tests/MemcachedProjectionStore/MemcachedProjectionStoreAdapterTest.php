<?php
declare(strict_types=1);

namespace BusFactor\MemcachedProjectionStore;

use BusFactor\ProjectionStore\ProjectionStore;
use Memcached;
use PHPUnit\Framework\TestCase;

class MemcachedProjectionStoreAdapterTest extends TestCase
{
    /** @test */
    public function it_persists_with_memcached(): void
    {
        /** @var Memcached $memcached */
        $memcached = $this->createMock(Memcached::class);
        $store = new ProjectionStore(new MemcachedProjectionStoreAdapter($memcached));

        $store->store(new TestProjection('123'));
        $store->commit();
        $projection = $store->find('123', TestProjection::class);
        $this->assertEquals(new TestProjection('123'), $projection);
    }
}

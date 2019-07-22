<?php
declare(strict_types=1);

namespace BusFactor\LaravelCacheProjectionStore;

use BusFactor\ProjectionStore\ProjectionStore;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

class LaravelCacheProjectionStoreAdapterTest extends TestCase
{
    /** @test */
    public function it_persists_with_laravel_cache(): void
    {
        $cache = new Repository(new ArrayStore());
        $store = new ProjectionStore(new LaravelCacheProjectionStoreAdapter($cache));

        $store->store(new TestProjection('123'));
        $store->commit();
        $projection = $store->find('123', TestProjection::class);
        $this->assertEquals(new TestProjection('123'), $projection);
    }
}

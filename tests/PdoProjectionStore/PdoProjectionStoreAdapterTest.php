<?php
declare(strict_types=1);

namespace BusFactor\PdoProjectionStore;

use BusFactor\ObjectSerializer\ObjectSerializer;
use BusFactor\ObjectSerializer\SerializeFunctionObjectSerializer;
use BusFactor\PdoProxy\PdoProxy;
use BusFactor\ProjectionStore\ProjectionStore;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoProjectionStoreAdapterTest extends TestCase
{
    /** @test */
    public function it_persists_with_pdo(): void
    {
        $pdo = new PdoProxy(function (): PDO {
            $pdo = new PDO('sqlite::memory:');
            $pdo->exec('CREATE TABLE projection_store (
                projection_id VARCHAR,
                projection_class VARCHAR,
                projection_payload BLOB    
            )');
            return $pdo;
        });
        $serializer = new ObjectSerializer(new SerializeFunctionObjectSerializer());

        $store = new ProjectionStore(new PdoProjectionStoreAdapter($pdo, $serializer, new Config()));
        $store->store(new TestProjection('123'));
        $store->commit();

        $projection = $store->find('123', TestProjection::class);

        $this->assertEquals(new TestProjection('123'), $projection);
    }
}

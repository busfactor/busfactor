<?php

declare(strict_types=1);

namespace BusFactor\PdoAggregateStore;

use BusFactor\AggregateStore\AggregateStore;
use BusFactor\ObjectSerializer\ObjectSerializer;
use BusFactor\ObjectSerializer\SerializeFunctionObjectSerializer;
use BusFactor\PdoProxy\PdoProxy;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoAggregateStoreAdapterTest extends TestCase
{
    /** @test */
    public function it_persists_with_pdo(): void
    {
        $pdo = new PdoProxy(function (): PDO {
            $pdo = new PDO('sqlite::memory:');
            $pdo->exec('CREATE TABLE aggregates (
                aggregate_id VARCHAR,
                aggregate_type VARCHAR,
                aggregate_payload BLOB    
            )');
            return $pdo;
        });
        $config = (new Config())->withTable('aggregates');
        $serializer = new ObjectSerializer(new SerializeFunctionObjectSerializer());

        $store = new AggregateStore(new PdoAggregateStoreAdapter($pdo, $config, $serializer));
        $this->assertFalse($store->has('123', TestAggregate::getType()));

        $store->store(new TestAggregate('123'));
        $this->assertTrue($store->has('123', TestAggregate::getType()));
        $aggregate = $store->find('123', TestAggregate::getType());
        $this->assertInstanceOf(TestAggregate::class, $aggregate);
        $this->assertEquals('123', $aggregate->getAggregateId());

        $store->remove('123', TestAggregate::getType());
        $this->assertFalse($store->has('123', TestAggregate::getType()));
    }
}

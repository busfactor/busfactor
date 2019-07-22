<?php
declare(strict_types=1);

namespace BusFactor\PdoEventStore;

use BusFactor\EventStore\EventStore;
use BusFactor\EventStream\Envelope;
use BusFactor\EventStream\Metadata;
use BusFactor\EventStream\Stream;
use BusFactor\Pdo\PdoInterface;
use BusFactor\PdoProxy\PdoProxy;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoEventStoreTest extends TestCase
{
    /** @var PdoInterface */
    private $pdo;

    public function setUp()
    {
        parent::setUp();

        $this->pdo = new PdoProxy(function () {
            $pdo = new PDO('sqlite::memory:');
            $pdo->exec('CREATE TABLE event_store (
                sequence INT,
                stream_type VARCHAR,
                stream_id VARCHAR,
                stream_version INT,
                event_id VARCHAR,
                event_class VARCHAR,
                event_metadata VARCHAR,
                event_payload VARCHAR,
                event_time VARCHAR
            )');
            return $pdo;
        });
    }

    /** @test */
    public function it_stores_and_finds_stream(): void
    {
        $store = new EventStore(new PdoEventStoreAdapter($this->pdo, new Config()));

        $this->assertFalse($store->streamExists('123', 'type'));

        $store->append(
            (new Stream('123', 'type'))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 1))
        );

        $this->assertTrue($store->streamExists('123', 'type'));

        $store->append(
            (new Stream('123', 'type'))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 2))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 3))
        );

        $stream = $store->fetch('123', 'type');

        $this->assertEquals('123', $stream->getStreamId());
        $this->assertEquals('type', $stream->getStreamType());
        $this->assertEquals(3, $stream->getHighestVersion());
        $this->assertEquals(1, $stream->getLowestVersion());
        $this->assertCount(3, $stream->getEnvelopes());

        $version = $store->getVersion('123', 'type');
        $this->assertEquals(3, $version);

        $store->purge();
        $this->assertFalse($store->streamExists('123', 'type'));
    }

    /** @test */
    public function events_are_inspected(): void
    {
        $store = new EventStore(new PdoEventStoreAdapter($this->pdo, new Config()));

        $store->append(
            (new Stream('123', 'type'))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 1))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 2))
                ->withEnvelope(Envelope::createNow(new TestEvent(), new Metadata(), 3))
        );

        $inspector = new TestInspector();
        $store->getAdapter()->inspect($inspector);

        $this->assertCount(3, $inspector->getInspectedEvents());
    }
}

<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\InMemoryProjectionStoreAdapter;
use BusFactor\ProjectionStore\ProjectionStore;
use PHPUnit\Framework\TestCase;

class ProjectionStoreTraceMiddlewareTest extends TestCase
{
    /** @test */
    public function it_stores_a_projection_without_tracing(): void
    {
        $trace = new ProjectionStoreTraceMiddleware();
        $trace->stopTracing();

        $store = new ProjectionStore(new InMemoryProjectionStoreAdapter());
        $store->addMiddleware($trace);
        $store->store($this->createProjection());

        $this->assertEmpty($trace->getTracedProjections());
    }

    /** @test */
    public function it_stores_a_projection_with_tracing(): void
    {
        $trace = new ProjectionStoreTraceMiddleware();
        $trace->startTracing();

        $store = new ProjectionStore(new InMemoryProjectionStoreAdapter());
        $store->addMiddleware($trace);
        $store->store($this->createProjection());

        $this->assertCount(1, $trace->getTracedProjections());

        $trace->clearTrace();
        $this->assertEmpty($trace->getTracedProjections());
    }

    /** @test */
    public function it_starts_and_stops_tracing(): void
    {
        $trace = new ProjectionStoreTraceMiddleware();
        $this->assertFalse($trace->isTracing());

        $trace->startTracing();
        $this->assertTrue($trace->isTracing());

        $trace->stopTracing();
        $this->assertFalse($trace->isTracing());
    }

    private function createProjection(): ProjectionInterface
    {
        return new class () implements ProjectionInterface {
            public function getId(): string
            {
                return __CLASS__;
            }
        };
    }
}

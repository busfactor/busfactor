<?php

declare(strict_types=1);

namespace BusFactor\Scenario;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\MiddlewareInterface;
use BusFactor\ProjectionStore\ProjectionStoreInterface;

class ProjectionStoreTraceMiddleware implements MiddlewareInterface
{
    /** @var ProjectionInterface[] */
    private $traceStack = [];

    /** @var bool */
    private $tracing = false;

    public function find(string $id, string $class, ProjectionStoreInterface $next): ProjectionInterface
    {
        return $next->find($id, $class);
    }

    public function has(string $id, string $class, ProjectionStoreInterface $next): bool
    {
        return $next->has($id, $class);
    }

    public function store(ProjectionInterface $projection, ProjectionStoreInterface $next): void
    {
        if ($this->tracing) {
            $this->addOrReplaceProjection($projection);
        }
        $next->store($projection);
    }

    public function remove(string $id, string $class, ProjectionStoreInterface $next): void
    {
        if ($this->tracing) {
            $this->removeProjection($id, $class);
        }
        $next->remove($id, $class);
    }

    public function purge(ProjectionStoreInterface $next): void
    {
        $next->purge();
    }

    public function commit(ProjectionStoreInterface $next): void
    {
        $next->commit();
    }

    public function rollback(ProjectionStoreInterface $next): void
    {
        $next->rollback();
    }

    public function startTracing(): void
    {
        if ($this->tracing) {
            return;
        }
        $this->tracing = true;
        $this->traceStack = [];
    }

    public function stopTracing(): void
    {
        $this->tracing = false;
    }

    public function clearTrace(): void
    {
        $this->traceStack = [];
    }

    public function isTracing(): bool
    {
        return $this->tracing;
    }

    /** @return ProjectionInterface[] */
    public function getTracedProjections(): array
    {
        return array_values($this->traceStack);
    }

    private function addOrReplaceProjection(ProjectionInterface $projection): void
    {
        $key = self::resolveKey($projection->getId(), get_class($projection));
        $this->traceStack[$key] = $projection;
    }

    private function removeProjection(string $id, string $class): void
    {
        $key = self::resolveKey($id, $class);
        unset($this->traceStack[$key]);
    }

    private static function resolveKey(string $id, string $class): string
    {
        return sprintf('%s:%s', $id, $class);
    }
}

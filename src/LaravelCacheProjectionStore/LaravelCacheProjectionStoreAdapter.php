<?php
declare(strict_types=1);

namespace BusFactor\LaravelCacheProjectionStore;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\AdapterInterface;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;
use Illuminate\Cache\Repository;

class LaravelCacheProjectionStoreAdapter implements AdapterInterface
{
    /** @var Repository */
    private $cache;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        $key = $this->resolveKey($id, $class);
        $projection = $this->cache->get($key);
        if (!$projection) {
            throw ProjectionNotFoundException::forProjection($class, $id);
        }

        return $projection;
    }

    public function findBy(string $class): Generator
    {
        $keys = $this->cache->get('projection-store:' . $class);
        if (!$keys) {
            $keys = [];
        }
        foreach ($keys as $key) {
            yield $this->cache->get($key);
        }
    }

    public function has(string $id, string $class): bool
    {
        $key = $this->resolveKey($id, $class);
        return $this->cache->has($key);
    }

    public function commit(UnitOfWork $unit): void
    {
        foreach ($unit->getStored() as $projection) {
            $this->store($projection);
        }
        foreach ($unit->getRemoved() as $descriptor) {
            $this->remove($descriptor->getId(), $descriptor->getClass());
        }
    }

    public function purge(): void
    {
        $keys = $this->cache->get('projection-store:keys', []);
        foreach ($keys as $key) {
            $this->cache->delete($key);
        }

        $classes = $this->cache->get('projection-store:classes');
        if ($classes) {
            foreach ($classes as $class) {
                $this->cache->delete('projection-store:' . $class);
            }
        }
        $this->cache->forever('projection-store:keys', []);
    }

    private function store(ProjectionInterface $projection): void
    {
        $class = get_class($projection);
        $id = $projection->getId();
        $key = $this->resolveKey($id, $class);
        $this->cache->forever($key, $projection);

        $classes = $this->cache->get('projection-store:classes', []);
        if (!in_array($class, $classes)) {
            $classes[] = $class;
            $this->cache->forever('projection-store:classes', $classes);
        }

        $keys = $this->cache->get('projection-store:keys', []);
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->cache->forever('projection-store:keys', $keys);
        }

        $keys = $this->cache->get('projection-store:' . $class, []);
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->cache->forever('projection-store:' . $class, $keys);
        }
    }

    private function remove(string $id, string $class): void
    {
        $key = $this->resolveKey($id, $class);
        $this->cache->delete($key);

        $keys = $this->cache->get('projection-store:' . $class);
        unset($keys[$key]);
        $this->cache->forever('projection-store:' . $class, $keys);

        $keys = $this->cache->get('projection-store:keys');
        unset($keys[$key]);
        $this->cache->forever('projection-store:keys', $keys);
    }

    private function resolveKey(string $id, ?string $class = null): string
    {
        if ($class) {
            return sprintf('projection-store:%s:%s', $id, $class);
        } else {
            return sprintf('projection-store:%s', $class);
        }
    }
}

<?php

declare(strict_types=1);

namespace BusFactor\MemcachedProjectionStore;

use BusFactor\Projection\ProjectionInterface;
use BusFactor\ProjectionStore\AdapterInterface;
use BusFactor\ProjectionStore\ProjectionNotFoundException;
use BusFactor\ProjectionStore\UnitOfWork;
use Generator;
use Memcached;

class MemcachedProjectionStoreAdapter implements AdapterInterface
{
    /** @var Memcached */
    private $memcached;

    /** @var string */
    private $namespace;

    public function __construct(Memcached $memcached, string $namespace = 'projection-store')
    {
        $this->memcached = $memcached;
        $this->namespace = $namespace;
    }

    public function find(string $id, string $class): ProjectionInterface
    {
        $key = $this->resolveKey($id, $class);
        $projection = $this->memcached->get($key);
        if (!$projection) {
            throw ProjectionNotFoundException::forProjection($class, $id);
        }

        return $projection;
    }

    public function findBy(string $class): Generator
    {
        $keys = $this->memcached->get($class);
        if (!$keys) {
            $keys = [];
        }
        foreach ($keys as $key) {
            yield $this->memcached->get($key);
        }
    }

    public function has(string $id, string $class): bool
    {
        $key = $this->resolveKey($id, $class);
        return (bool) $this->memcached->get($key);
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
        $keys = $this->memcached->get($this->namespace . ':keys');
        if (!$keys) {
            $keys = [];
        }
        foreach ($keys as $key) {
            $this->memcached->delete($key);
        }

        $classes = $this->memcached->get($this->namespace . ':classes');
        if ($classes) {
            foreach ($classes as $class) {
                $this->memcached->delete($class);
            }
        }
        $this->memcached->set($this->namespace . ':keys', [], 0);
    }

    private function store(ProjectionInterface $projection): void
    {
        $class = get_class($projection);
        $id = $projection->getId();
        $key = $this->resolveKey($id, $class);
        $this->memcached->set($key, $projection, 0);

        $classes = $this->memcached->get($this->namespace . ':classes');
        if (!$classes) {
            $classes = [];
        }
        if (!in_array($class, $classes)) {
            $classes[] = $class;
            $this->memcached->set($this->namespace . ':classes', $classes, 0);
        }

        $keys = $this->memcached->get($this->namespace . ':keys');
        if (!$keys) {
            $keys = [];
        }
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->memcached->set($this->namespace . ':keys', $keys, 0);
        }

        $keys = $this->memcached->get($class);
        if (!$keys) {
            $keys = [];
        }
        if (!in_array($key, $keys)) {
            $keys[$key] = $key;
            $this->memcached->set($class, $keys, 0);
        }
    }

    private function remove(string $id, string $class): void
    {
        $key = $this->resolveKey($id, $class);
        $this->memcached->delete($key);

        $keys = $this->memcached->get($class);
        unset($keys[$key]);
        $this->memcached->set($class, $keys, 0);

        $keys = $this->memcached->get($this->namespace . ':keys');
        unset($keys[$key]);
        $this->memcached->set($this->namespace . ':keys', $keys, 0);
    }

    private function resolveKey(string $id, string $class): string
    {
        return sprintf($this->namespace . ':projections:%s:%s', $id, $class);
    }
}

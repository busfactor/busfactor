<?php
declare(strict_types=1);

namespace BusFactor\EventStream;

class Metadata
{
    /** @var array */
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }
        return null;
    }

    public function with(string $key, $value): self
    {
        $clone = clone $this;
        $clone->data[$key] = $value;

        return $clone;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}

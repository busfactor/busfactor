<?php
declare(strict_types=1);

namespace BusFactor\ObjectSerializer;

class Output
{
    /** @var string[] */
    private $messages = [];

    public function write(string $message): void
    {
        $this->messages[] = $message;
    }

    public function read(): array
    {
        return $this->messages;
    }

    public function clear(): void
    {
        $this->messages = [];
    }
}

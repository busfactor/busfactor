<?php
declare(strict_types=1);

namespace BusFactor\Uuid;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public static function new(): string
    {
        return RamseyUuid::uuid4()->toString();
    }

    public static function isValid(string $uuid): bool
    {
        return RamseyUuid::isValid($uuid);
    }
}

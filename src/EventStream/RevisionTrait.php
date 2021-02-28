<?php

declare(strict_types=1);

namespace BusFactor\EventStream;

trait RevisionTrait
{
    public static function getRevision(): int
    {
        return self::REVISION;
    }
}

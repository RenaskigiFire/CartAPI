<?php

namespace App\Tests\Domain\ValueObject\Shared;

use App\Domain\ValueObject\Shared\UniqueId;

class UniqueIdMother
{
    public static function apply(
        string $value,
    ): UniqueId
    {
        return new UniqueId($value);
    }

    public static function random(): UniqueId
    {
        $data    = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        $value   = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        return self::apply($value);
    }
}

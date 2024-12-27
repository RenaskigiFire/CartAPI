<?php

namespace App\Tests\Domain\ValueObject\Cart;

use App\Domain\ValueObject\Cart\CartUnits;

class CartUnitsMother
{
    public static function apply(int $value): CartUnits
    {
        return new CartUnits($value);
    }

    public static function random(): CartUnits
    {
        return self::apply(random_int(1, 100));
    }
}

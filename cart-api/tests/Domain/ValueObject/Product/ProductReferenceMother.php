<?php

namespace App\Tests\Domain\ValueObject\Product;

use App\Domain\ValueObject\Product\ProductReference;

class ProductReferenceMother
{
    public static function apply(string $value): ProductReference
    {
        return new ProductReference($value);
    }

    public static function random(): ProductReference
    {
        $number = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        return self::apply('P' . $number);
    }
}

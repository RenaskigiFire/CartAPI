<?php

namespace App\Tests\Domain\Entity\Cart;

use App\Domain\Entity\Cart\CartContent;
use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Cart\CartUnits;
use App\Tests\Domain\Entity\Product\ProductMother;
use App\Tests\Domain\ValueObject\Cart\CartUnitsMother;

class CartContentMother
{
    public static function apply(Product $product, CartUnits $units): CartContent
    {
        return new CartContent(
            $product,
            $units
        );
    }

    public static function random(): CartContent
    {
        return self::apply(
            ProductMother::random(),
            CartUnitsMother::random()
        );
    }
}

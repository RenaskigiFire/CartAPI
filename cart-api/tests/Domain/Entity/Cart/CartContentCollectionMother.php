<?php

namespace App\Tests\Domain\Entity\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Cart\CartContentCollection;

class CartContentCollectionMother
{
    public static function random(Cart $cart, int $count = 3): CartContentCollection
    {
        $collection = new CartContentCollection();
        for ($i = 0; $i < $count; $i++) {
            $collection->addCartContent(CartContentMother::random());
        }
        return $collection;
    }
}

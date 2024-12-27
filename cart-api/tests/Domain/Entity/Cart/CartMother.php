<?php

namespace App\Tests\Domain\Entity\Cart;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\Shared\UniqueId;
use App\Tests\Domain\ValueObject\Shared\UniqueIdMother;

class CartMother
{
    public static function apply(
        UniqueId $id,
        UniqueId $sessionId,
        ?UniqueId $userId = null
    ): Cart
    {
        return new Cart(
            $id,
            $sessionId,
            $userId
        );
    }

    public static function random(bool $hasUser = false): Cart
    {
        $userId = null;
        if ($hasUser) {
            $userId = UniqueIdMother::random();
        }
        return self::apply(
            UniqueIdMother::random(),
            UniqueIdMother::random(),
            $userId
        );
    }

    public static function randomWithContent(ProductRepositoryInterface $productRepository = null): Cart
    {
        $cart = CartMother::random();
        $cartContentCollection = CartContentCollectionMother::random($cart);
        foreach ($cartContentCollection as $cartContent) {
            $productRepository?->save($cartContent->getProduct());
            $cart->incrementProductCount($cartContent->getUnits());
        }
        $cart->setCartContents($cartContentCollection);
        return $cart;
    }
}

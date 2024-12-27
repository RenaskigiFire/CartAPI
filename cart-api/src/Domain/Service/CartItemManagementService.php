<?php

namespace App\Domain\Service;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Entity\Cart\CartContent;
use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Cart\CartUnits;

class CartItemManagementService
{
    public function addProductToCart(Cart $cart, Product $product, int $units): void
    {
        $existingCartContent = $cart->getCartContents()->findByProductReference($product->getReference());

        if ($existingCartContent) {
            $existingCartContent->setUnits($existingCartContent->getUnits() + $units);
        } else {
            $cart->getCartContents()->addCartContent(new CartContent($product, new CartUnits($units)));
        }
        $cart->incrementProductCount($units);
    }

    public function removeProductFromCart(Cart $cart, Product $product, int $units): void
    {
        $existingCartContent = $cart->getCartContents()->findByProductReference($product->getReference());

        if ($existingCartContent) {
            $newUnits = $existingCartContent->getUnits() - $units;
            if($newUnits <= 0) {
                $cart->getCartContents()->removeByProductReference($product->getReference());
            } else {
                $existingCartContent->setUnits($newUnits);
            }
            $cart->decrementProductCount($units);
        }
    }
}

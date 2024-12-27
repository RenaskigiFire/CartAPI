<?php

namespace App\Domain\Entity\Cart;

use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Cart\CartUnits;

class CartContent
{
    public function __construct(
        private readonly Product $product,
        private CartUnits $units,
    )
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getUnits(): int
    {
        return $this->units->value;
    }

    public function setUnits(int $units): void
    {
        $this->units = new CartUnits($units);
    }
}
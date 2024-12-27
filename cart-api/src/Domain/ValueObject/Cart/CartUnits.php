<?php

namespace App\Domain\ValueObject\Cart;

class CartUnits
{
    public int $value;

    public function __construct(
        int $value
    )
    {
        if ($value < 1) {
            throw new \Exception('Cart unit must be greater than 1');
        }
        $this->value = $value;
    }
}

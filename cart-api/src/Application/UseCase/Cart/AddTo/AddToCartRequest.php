<?php

namespace App\Application\UseCase\Cart\AddTo;

readonly class AddToCartRequest
{
    public function __construct(
        public string $cartId,
        public string $productReference,
        public int    $quantity,
    )
    {
    }
}

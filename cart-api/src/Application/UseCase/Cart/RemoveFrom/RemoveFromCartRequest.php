<?php

namespace App\Application\UseCase\Cart\RemoveFrom;

readonly class RemoveFromCartRequest
{
    public function __construct(
        public string $cartId,
        public string $productReference,
        public int    $quantity,
    )
    {
    }
}

<?php

namespace App\Infrastructure\Service\Transformer;

use App\Domain\Entity\Cart\Cart;

class CartDataTransformer
{
    public function transform(Cart $cart): array
    {
        return [
            'id' => $cart->getId(),
            'session_id' => $cart->getSessionId(),
            'user_id' => $cart->getUserId(),
            'product_count' => $cart->getProductCount(),
            'contents' => $cart->getCartContents()->jsonSerialize()
        ];
    }
}
<?php

namespace App\Application\UseCase\Cart\Get;

use App\Domain\Repository\CartRepositoryInterface;

class GetCartUseCase
{
    public function __construct(
        private readonly CartRepositoryInterface    $cartRepository,
    )
    {
    }

    public function execute(string $cartId): GetCartResponse
    {
        try {
            $cart    = $this->cartRepository->findOrFail($cartId);

            return GetCartResponse::createValidResponse(cart: $cart);
        } catch (\Exception $exception) {
            return GetCartResponse::createInvalidResponse($exception->getMessage());
        }
    }
}

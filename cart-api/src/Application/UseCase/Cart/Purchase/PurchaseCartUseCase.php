<?php

namespace App\Application\UseCase\Cart\Purchase;

use App\Domain\Repository\CartRepositoryInterface;

class PurchaseCartUseCase
{
    public function __construct(
        private readonly CartRepositoryInterface    $cartRepository,
    )
    {
    }

    public function execute(string $cartId): PurchaseCartResponse
    {
        try {
            $cart    = $this->cartRepository->findOrFail($cartId);

            // TODO: Transform Cart to order, or create and send event to do it

            $this->cartRepository->delete($cart);

            return PurchaseCartResponse::createValidResponse();
        } catch (\Exception $exception) {
            return PurchaseCartResponse::createInvalidResponse($exception->getMessage());
        }
    }
}
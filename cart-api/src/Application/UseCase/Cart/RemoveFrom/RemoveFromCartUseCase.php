<?php

namespace App\Application\UseCase\Cart\RemoveFrom;

use App\Domain\Repository\CartRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Service\CartItemManagementService;

class RemoveFromCartUseCase
{
    public function __construct(
        private readonly CartRepositoryInterface    $cartRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CartItemManagementService  $cartItemManagementService
    )
    {
    }

    public function execute(RemoveFromCartRequest $request): RemoveFromCartResponse
    {
        try {
            $cart    = $this->cartRepository->findOrFail($request->cartId);
            $product = $this->productRepository->findOrFail($request->productReference);

            $this->cartItemManagementService->removeProductFromCart($cart, $product, $request->quantity);

            $this->cartRepository->save($cart);

            return RemoveFromCartResponse::createValidResponse(cart: $cart);
        } catch (\Exception $exception) {
            return RemoveFromCartResponse::createInvalidResponse($exception->getMessage());
        }
    }
}

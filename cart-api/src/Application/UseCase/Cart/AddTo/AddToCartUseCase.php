<?php

namespace App\Application\UseCase\Cart\AddTo;

use App\Domain\Repository\CartRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Service\CartItemManagementService;

class AddToCartUseCase
{
    public function __construct(
        private readonly CartRepositoryInterface    $cartRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CartItemManagementService  $cartItemManagementService
    )
    {
    }

    public function execute(AddToCartRequest $request): AddToCartResponse
    {
        try {
            $cart    = $this->cartRepository->findOrFail($request->cartId);
            $product = $this->productRepository->findOrFail($request->productReference);

            $this->cartItemManagementService->addProductToCart($cart, $product, $request->quantity);

            $this->cartRepository->save($cart);

            return AddToCartResponse::createValidResponse(cart: $cart);
        } catch (\Exception $exception) {
            return AddToCartResponse::createInvalidResponse($exception->getMessage());
        }
    }
}

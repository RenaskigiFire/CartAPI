<?php

namespace App\Application\UseCase\Cart\Create;

use App\Domain\Repository\CartRepositoryInterface;
use App\Domain\Service\Builder\CartBuilder;

class CreateCartUseCase
{
    public function __construct(
        private readonly CartBuilder             $cartBuilder,
        private readonly CartRepositoryInterface $cartRepository
    )
    {
    }

    public function execute(CreateCartRequest $request): CreateCartResponse
    {
        try {
            // TODO: Add logic to check if cart exist with this sessionId or userId get it and don't create another
            $cart = $this->cartBuilder->build(
                sessionId: $request->sessionId,
                userId   : $request->userId
            );
            $this->cartRepository->save($cart);

            return CreateCartResponse::createValidResponse(cartId: $cart->getId());
        } catch (\Exception $exception) {
            return CreateCartResponse::createInvalidResponse($exception->getMessage());
        }
    }
}
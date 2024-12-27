<?php

namespace App\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\RemoveFrom\RemoveFromCartRequest;
use App\Application\UseCase\Cart\RemoveFrom\RemoveFromCartUseCase;
use App\Domain\Entity\Cart\Cart;
use App\Infrastructure\Service\Transformer\CartDataTransformer;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveFromCartAction
{
    // TODO: Add token and security
    private const CART_ID               = 'cart_id';
    private const PRODUCT_REFERENCE_KEY = 'product_reference';
    private const QUANTITY_KEY          = 'quantity';
    private const REQUEST_KEYS          = [self::CART_ID, self::PRODUCT_REFERENCE_KEY, self::QUANTITY_KEY];

    public function __construct(
        private readonly HttpRequestContentValidator $httpRequestContentValidator,
        private readonly RemoveFromCartUseCase       $removeFromCartUseCase,
        private readonly CartDataTransformer         $cartDataTransformer
    )
    {
    }

    #[Route('/api/v1/cart/remove-from', name: 'cart_remove_from', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $requestArray = json_decode(
                json       : $request->getContent(),
                associative: true,
                flags      : JSON_THROW_ON_ERROR
            );

            if (!$this->httpRequestContentValidator->validate($requestArray, self::REQUEST_KEYS)) {
                return $this->jsonResponse('Bad Request', Response::HTTP_BAD_REQUEST);
            }

            $request = new RemoveFromCartRequest(
                cartId          : $requestArray[self::CART_ID],
                productReference: $requestArray[self::PRODUCT_REFERENCE_KEY],
                quantity        : $requestArray[self::QUANTITY_KEY]
            );

            $response = $this->removeFromCartUseCase->execute($request);

            if ($response->isValid()) {
                return $this->jsonResponse($response->getResponseMessage(), $response->getResponseCode(), $response->cart);
            }

            return $this->jsonResponse($response->getResponseMessage(), $response->getResponseCode());
        } catch (\Throwable $exception) {
            return $this->jsonResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function jsonResponse(string $message, int $responseCode, ?Cart $cart = null): JsonResponse
    {
        $jsonCart = $cart ? $this->cartDataTransformer->transform($cart) : null;

        return new JsonResponse(["message" => $message, "cart" => $jsonCart], $responseCode);
    }
}
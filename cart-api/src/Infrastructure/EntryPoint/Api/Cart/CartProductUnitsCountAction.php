<?php

namespace App\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\AddTo\AddToCartRequest;
use App\Application\UseCase\Cart\AddTo\AddToCartUseCase;
use App\Application\UseCase\Cart\Get\GetCartUseCase;
use App\Domain\Entity\Cart\Cart;
use App\Infrastructure\Service\Transformer\CartDataTransformer;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartProductUnitsCountAction
{
    // TODO: Add token and security
    private const CART_ID               = 'cart_id';
    private const REQUEST_KEYS          = [self::CART_ID];

    public function __construct(
        private readonly HttpRequestContentValidator $httpRequestContentValidator,
        private readonly GetCartUseCase            $getCartUseCase
    )
    {
    }

    #[Route('/api/v1/cart/product-units-count', name: 'cart_product_units_count', methods: ['POST'])]
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

            $response = $this->getCartUseCase->execute($requestArray[self::CART_ID]);

            if ($response->isValid()) {
                return $this->jsonResponse($response->getResponseMessage(), $response->getResponseCode(), $response->cart->getProductCount());
            }

            return $this->jsonResponse($response->getResponseMessage(), $response->getResponseCode());
        } catch (\Throwable $exception) {
            return $this->jsonResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function jsonResponse(string $message, int $responseCode, ?int $cartProductCount = null): JsonResponse
    {
        return new JsonResponse(["message" => $message, "cart_product_count" => $cartProductCount], $responseCode);
    }
}
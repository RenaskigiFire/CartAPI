<?php

namespace App\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\Purchase\PurchaseCartUseCase;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseCartAction
{
    // TODO: Add token and security
    private const CART_ID      = 'cart_id';
    private const REQUEST_KEYS = [self::CART_ID];

    public function __construct(
        private readonly HttpRequestContentValidator $httpRequestContentValidator,
        private readonly PurchaseCartUseCase         $purchaseCartUseCase,
    )
    {
    }

    #[Route('/api/v1/cart/purchase', name: 'cart_purchase', methods: ['POST'])]
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

            $response = $this->purchaseCartUseCase->execute($requestArray[self::CART_ID]);

            return $this->jsonResponse($response->getResponseMessage(), $response->getResponseCode());
        } catch (\Throwable $exception) {
            return $this->jsonResponse($exception->getMessage(), $exception->getCode());
        }
    }

    private function jsonResponse(string $message, int $responseCode): JsonResponse
    {
        return new JsonResponse(["message" => $message], $responseCode);
    }
}
<?php

namespace App\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\Create\CreateCartRequest;
use App\Application\UseCase\Cart\Create\CreateCartUseCase;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateCartAction
{
    // TODO: Add token and security
    private const SESSION_ID_KEY = 'session_id';
    private const USER_ID_KEY = 'user_id';
    private const REQUEST_KEYS = [self::SESSION_ID_KEY, self::USER_ID_KEY];

    public function __construct(
        private readonly HttpRequestContentValidator $httpRequestContentValidator,
        private readonly CreateCartUseCase $createCartUseCase,
    )
    {
    }

    #[Route('/api/v1/cart/create', name: 'cart_create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $requestArray = json_decode(
                json       : $request->getContent(),
                associative: true,
                flags      : JSON_THROW_ON_ERROR
            );

            if (!$this->httpRequestContentValidator->validate($requestArray, self::REQUEST_KEYS)) {
                return $this->jsonResponse('Bad Request',Response::HTTP_BAD_REQUEST);
            }

            $request = new CreateCartRequest(
                sessionId: $requestArray[self::SESSION_ID_KEY],
                userId: $requestArray[self::USER_ID_KEY]
            );

            $response = $this->createCartUseCase->execute($request);

            if($response->isValid()){
                return $this->jsonResponse($response->getResponseMessage(),$response->getResponseCode(),$response->cartId);
            }

            return $this->jsonResponse($response->getResponseMessage(),$response->getResponseCode());
        } catch (\Throwable $exception) {
            return $this->jsonResponse($exception->getMessage(),$exception->getCode());
        }
    }

    private function jsonResponse(string $message, int $responseCode, ?string $cartId = null): JsonResponse
    {
        return new JsonResponse(["message" => $message, "cart_id" => $cartId], $responseCode);
    }
}
<?php

namespace App\Infrastructure\EntryPoint\Api\Product;

use App\Application\UseCase\Product\Create\CreateProductRequest;
use App\Application\UseCase\Product\Create\CreateProductUseCase;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateProductAction
{
    // TODO: Add token and security
    private const PRODUCT_REFERENCE_KEY = 'product_reference';
    private const NAME_KEY              = 'name';
    private const REQUEST_KEYS          = [self::PRODUCT_REFERENCE_KEY, self::NAME_KEY];

    public function __construct(
        private readonly HttpRequestContentValidator $httpRequestContentValidator,
        private readonly CreateProductUseCase $createProductUseCase,
    )
    {
    }

    #[Route('/api/v1/product/create', name: 'product_create', methods: ['POST'])]
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

            $request = new CreateProductRequest(
                productReference: $requestArray[self::PRODUCT_REFERENCE_KEY],
                name: $requestArray[self::NAME_KEY]
            );

            $response = $this->createProductUseCase->execute($request);

            if($response->isValid()){
                return $this->jsonResponse($response->getResponseMessage(),$response->getResponseCode(),$response->productReference);
            }

            return $this->jsonResponse($response->getResponseMessage(),$response->getResponseCode());
        } catch (\Throwable $exception) {
            return $this->jsonResponse($exception->getMessage(),$exception->getCode());
        }
    }

    private function jsonResponse(string $message, int $responseCode, ?string $productReference = null): JsonResponse
    {
        return new JsonResponse(["message" => $message, "product_reference" => $productReference], $responseCode);
    }
}
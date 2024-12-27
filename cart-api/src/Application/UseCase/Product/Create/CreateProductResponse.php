<?php

namespace App\Application\UseCase\Product\Create;

use App\Application\UseCase\AbstractUseCaseResponse;

class CreateProductResponse extends AbstractUseCaseResponse
{

    public function __construct(
        int                     $responseCode,
        string                  $responseMessage = '',
        public readonly ?string $productReference = null
    )
    {
        parent::__construct($responseCode, $responseMessage);
    }

    public static function createValidResponse(string $productReference): CreateProductResponse
    {
        return new self(
            responseCode    : parent::VALID_RESPONSE_CODE,
            responseMessage : 'Success on creating new product',
            productReference: $productReference
        );
    }

    public static function createInvalidResponse(string $message)
    {
        return new self(
            responseCode   : parent::INVALID_RESPONSE_CODE,
            responseMessage: $message
        );
    }

    public function isValid(): bool
    {
        return $this->responseCode === parent::VALID_RESPONSE_CODE;
    }
}
<?php

namespace App\Application\UseCase\Cart\Create;

use App\Application\UseCase\AbstractUseCaseResponse;

class CreateCartResponse extends AbstractUseCaseResponse
{

    public function __construct(
        int                     $responseCode,
        string                  $responseMessage = '',
        public readonly ?string $cartId = null
    )
    {
        parent::__construct($responseCode, $responseMessage);
    }

    public static function createValidResponse(string $cartId): CreateCartResponse
    {
        return new self(
            responseCode   : parent::VALID_RESPONSE_CODE,
            responseMessage: 'Success on creating new cart',
            cartId         : $cartId
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
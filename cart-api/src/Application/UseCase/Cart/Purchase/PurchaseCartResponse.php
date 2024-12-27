<?php

namespace App\Application\UseCase\Cart\Purchase;

use App\Application\UseCase\AbstractUseCaseResponse;

class PurchaseCartResponse extends AbstractUseCaseResponse
{

    public function __construct(
        int                   $responseCode,
        string                $responseMessage = ''
    )
    {
        parent::__construct($responseCode, $responseMessage);
    }

    public static function createValidResponse(): PurchaseCartResponse
    {
        return new self(
            responseCode   : parent::VALID_RESPONSE_CODE,
            responseMessage: 'Cart purchased successfully.'
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

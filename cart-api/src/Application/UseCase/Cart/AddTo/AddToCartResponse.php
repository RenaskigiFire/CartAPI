<?php

namespace App\Application\UseCase\Cart\AddTo;

use App\Application\UseCase\AbstractUseCaseResponse;
use App\Domain\Entity\Cart\Cart;

class AddToCartResponse extends AbstractUseCaseResponse
{

    public function __construct(
        int                   $responseCode,
        string                $responseMessage = '',
        public readonly ?Cart $cart = null
    )
    {
        parent::__construct($responseCode, $responseMessage);
    }

    public static function createValidResponse(Cart $cart): AddToCartResponse
    {
        return new self(
            responseCode   : parent::VALID_RESPONSE_CODE,
            responseMessage: 'Product added to cart',
            cart           : $cart
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

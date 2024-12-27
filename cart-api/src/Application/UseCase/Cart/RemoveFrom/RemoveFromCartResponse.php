<?php

namespace App\Application\UseCase\Cart\RemoveFrom;

use App\Application\UseCase\AbstractUseCaseResponse;
use App\Domain\Entity\Cart\Cart;

class RemoveFromCartResponse extends AbstractUseCaseResponse
{

    public function __construct(
        int                   $responseCode,
        string                $responseMessage = '',
        public readonly ?Cart $cart = null
    )
    {
        parent::__construct($responseCode, $responseMessage);
    }

    public static function createValidResponse(Cart $cart): RemoveFromCartResponse
    {
        return new self(
            responseCode   : parent::VALID_RESPONSE_CODE,
            responseMessage: 'Product removed from cart',
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

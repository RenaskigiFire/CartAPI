<?php

namespace App\Application\UseCase\Cart\Create;

readonly class CreateCartRequest
{
    public function __construct(
        public string  $sessionId,
        public ?string $userId = null,
    )
    {
    }
}

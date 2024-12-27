<?php

namespace App\Application\UseCase\Product\Create;

readonly class CreateProductRequest
{
    public function __construct(
        public string $productReference,
        public string $name,
    )
    {
    }
}

<?php

namespace App\Domain\Entity\Product;

use App\Domain\ValueObject\Product\ProductReference;

class Product
{
    public function __construct(
        private readonly ProductReference $reference,
        private readonly string $name
    )
    {
    }

    public function getReference(): string
    {
        return $this->reference->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

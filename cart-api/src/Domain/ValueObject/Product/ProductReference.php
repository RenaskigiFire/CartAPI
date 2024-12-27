<?php

namespace App\Domain\ValueObject\Product;

class ProductReference
{
    public string $value;

    public function __construct(
        string $value
    )
    {
        $pattern = '/^[P]\d{6}$/';
        if (!preg_match($pattern, $value)) {
            throw new \Exception('Invalid product reference ' . $value);
        }
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

<?php

namespace App\Domain\Entity\Cart;

use App\Domain\Collection\TypedCollection;

class CartContentCollection extends TypedCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct(CartContent::class, $items);
    }

    public function findByProductReference(string $productReference): ?CartContent
    {
        return $this->get($productReference);
    }

    public function addCartContent(CartContent $cartContent): void
    {
        $this->add($cartContent->getProduct()->getReference(), $cartContent);
    }

    public function removeByProductReference(string $productReference): void
    {
        $this->remove($productReference);
    }

    public function jsonSerialize(): array
    {
        return array_map(fn(CartContent $content) => [
            'product_reference' => $content->getProduct()->getReference(),
            'product_name' => $content->getProduct()->getName(),
            'units' => $content->getUnits()
        ], $this->items);
    }
}

<?php

namespace App\Domain\Entity\Cart;

use App\Domain\ValueObject\Shared\UniqueId;

class Cart
{
    private string                $id;
    private UniqueId              $sessionId;
    private ?UniqueId             $userId;
    private CartContentCollection $cartContents;
    private int                   $productCount;

    public function __construct(
        UniqueId  $id,
        UniqueId  $sessionId,
        ?UniqueId $userId = null
    )
    {
        $this->id           = $id->value;
        $this->sessionId    = $sessionId;
        $this->userId       = $userId;
        $this->productCount = 0;
        $this->cartContents = new CartContentCollection();

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId->value;
    }

    public function getUserId(): ?string
    {
        return $this->userId?->value;
    }

    public function setCartContents(CartContentCollection $cartContents): void
    {
        $this->cartContents = $cartContents;
    }

    public function getCartContents(): CartContentCollection
    {
        return $this->cartContents;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function incrementProductCount(int $productCount): void
    {
        $this->productCount += $productCount;
    }

    public function decrementProductCount(int $productCount): void
    {
        $this->productCount -= $productCount;
    }
}

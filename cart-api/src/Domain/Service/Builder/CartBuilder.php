<?php

namespace App\Domain\Service\Builder;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Service\Generator\IdentityGeneratorInterface;
use App\Domain\ValueObject\Shared\UniqueId;

class CartBuilder
{
    public function __construct(
        private readonly IdentityGeneratorInterface $identityGenerator
    )
    {
    }

    public function build(string $sessionId, ?string $userId): Cart
    {
        $id = $this->identityGenerator->generateId();
        return new Cart(
            id: $id,
            sessionId: new UniqueId($sessionId),
            userId: new UniqueId($userId),
        );
    }
}

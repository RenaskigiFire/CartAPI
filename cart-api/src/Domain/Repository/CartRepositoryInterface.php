<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Cart\Cart;

interface CartRepositoryInterface
{
    public function findOrFail(string $uniqueId): Cart;
    public function save(Cart $cart): void;
    public function delete(Cart $cart): void;
}

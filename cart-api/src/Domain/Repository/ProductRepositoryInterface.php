<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product\Product;

interface ProductRepositoryInterface
{
    public function findOrFail(string $uniqueId): Product;
    public function save(Product $product): void;
}

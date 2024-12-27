<?php

namespace App\Tests\Domain\Entity\Product;

use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Product\ProductReference;
use App\Tests\Domain\ValueObject\Product\ProductReferenceMother;

class ProductMother
{
    public static function apply(ProductReference $reference, string $name): Product
    {
        return new Product(
            $reference,
            $name
        );
    }

    public static function random(): Product
    {
        return self::apply(
            ProductReferenceMother::random(),
            self::generateRandomName()
        );
    }

    private static function generateRandomName(): string
    {
        $names = [
            'Running Shoes',
            'Soccer Jersey',
            'Basketball Shorts',
            'Yoga Pants',
            'Tennis Racket',
            'Baseball Cap',
            'Training Jacket',
            'Cycling Gloves',
            'Hiking Boots',
            'Workout Hoodie'
        ];

        return $names[array_rand($names)];
    }
}

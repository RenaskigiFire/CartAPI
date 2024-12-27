<?php

namespace App\Infrastructure\Persistence\Doctrine\Type\Product;

use App\Domain\ValueObject\Product\ProductReference;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ProductReferenceType extends Type
{
    public const PRODUCT_REFERENCE = "product_reference";

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ProductReference
    {
        return new ProductReference($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if(!$value){
            return null;
        }
        return $value instanceof ProductReference ? $value->value : (string)$value;
    }

    public function getName(): string
    {
        return self::PRODUCT_REFERENCE;
    }
}

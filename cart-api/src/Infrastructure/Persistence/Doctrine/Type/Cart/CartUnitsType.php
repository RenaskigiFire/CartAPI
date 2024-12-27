<?php

namespace App\Infrastructure\Persistence\Doctrine\Type\Cart;

use App\Domain\ValueObject\Cart\CartUnits;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CartUnitsType extends Type
{
    public const CART_UNITS = "cart_units";

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): CartUnits
    {
        return new CartUnits($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if(!$value){
            return null;
        }

        return $value instanceof CartUnits ? $value->value : (int)$value;
    }

    public function getName(): string
    {
        return self::CART_UNITS;
    }
}

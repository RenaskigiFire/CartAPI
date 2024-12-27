<?php

namespace App\Infrastructure\Persistence\Doctrine\Type\Shared;

use App\Domain\ValueObject\Shared\UniqueId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UniqueIdType extends Type
{
    public const UNIQUE_ID = "unique_id";

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): UniqueId
    {
        return new UniqueId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if(!$value){
            return null;
        }
        return $value instanceof UniqueId ? $value->value : (string)$value;
    }

    public function getName(): string
    {
        return self::UNIQUE_ID;
    }
}

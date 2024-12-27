<?php

namespace App\Infrastructure\Persistence\Doctrine\Type\Cart;

use App\Domain\Entity\Cart\CartContentCollection;
use App\Domain\Entity\Cart\CartContent;
use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Cart\CartUnits;
use App\Domain\ValueObject\Product\ProductReference;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CartContentCollectionType extends Type
{
    public const CART_CONTENT_COLLECTION = "cart_content_collection";

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): CartContentCollection
    {
        $data = json_decode($value, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException("Invalid JSON data for CartContentCollection");
        }

        $collection = new CartContentCollection();

        foreach ($data as $item) {
            if (!isset($item['product_reference'], $item['product_name'], $item['units'])) {
                throw new \InvalidArgumentException("Invalid CartContent data structure");
            }
            $collection->addCartContent(new CartContent(
                                            new Product(
                                                new ProductReference($item['product_reference']),
                                                $item['product_name']
                                            ),
                                            new CartUnits($item['units'])
                                        ));
        }

        return $collection;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof CartContentCollection) {
            throw new \InvalidArgumentException("Expected instance of CartContentCollection");
        }

        return json_encode($value->jsonSerialize());
    }

    public function getName(): string
    {
        return self::CART_CONTENT_COLLECTION;
    }
}

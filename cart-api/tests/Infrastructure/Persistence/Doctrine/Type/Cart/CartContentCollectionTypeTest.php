<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Type\Cart;

use App\Infrastructure\Persistence\Doctrine\Type\Cart\CartContentCollectionType;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Cart\CartContentCollection;
use App\Domain\Entity\Cart\CartContent;
use App\Domain\Entity\Product\Product;
use App\Domain\ValueObject\Cart\CartUnits;
use App\Domain\ValueObject\Product\ProductReference;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CartContentCollectionTypeTest extends TestCase
{
    private CartContentCollectionType $type;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new CartContentCollectionType();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testGetSQLDeclaration(): void
    {
        // Given
        $column = ['length' => 255];
        $this->platform->expects($this->once())
            ->method('getJsonTypeDeclarationSQL')
            ->with($column)
            ->willReturn('JSON');

        // When
        $result = $this->type->getSQLDeclaration($column, $this->platform);

        // Then
        $this->assertSame('JSON', $result);
    }

    public function testConvertToPHPValue(): void
    {
        // Given
        $json = '[
            {
                "product_reference": "P000001",
                "product_name": "Product 1",
                "units": 5
            },
            {
                "product_reference": "P000002",
                "product_name": "Product 2",
                "units": 3
            }
        ]';

        // When
        $collection = $this->type->convertToPHPValue($json, $this->platform);

        // Then
        $this->assertInstanceOf(CartContentCollection::class, $collection);
        $this->assertEquals(2, $collection->count());

        $content = $collection->first();
        $this->assertEquals('P000001', $content->getProduct()->getReference());
        $this->assertEquals('Product 1', $content->getProduct()->getName());
        $this->assertEquals(5, $content->getUnits());
    }

    public function testConvertToPHPValueWithMissingFields(): void
    {
        // Given
        $json = '[{"product_reference": "P000001", "product_name": "Product 1"}]';

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid CartContent data structure");

        // When
        $this->type->convertToPHPValue($json, $this->platform);
    }

    public function testConvertToPHPValueWithInvalidData(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid JSON data for CartContentCollection");

        // When
        $this->type->convertToPHPValue('invalid-json', $this->platform);
    }

    public function testConvertToDatabaseValue(): void
    {
        // Given
        $collection = new CartContentCollection();
        $collection->addCartContent(new CartContent(
                                        new Product(new ProductReference('P000001'), 'Product 1'),
                                        new CartUnits(5)
                                    ));

        // When
        $json = $this->type->convertToDatabaseValue($collection, $this->platform);

        // Then
        $this->assertJson($json);
        $data = json_decode($json, true);

        $this->assertCount(1, $data);
        $this->assertEquals('P000001', $data['P000001']['product_reference']);
        $this->assertEquals('Product 1', $data['P000001']['product_name']);
        $this->assertEquals(5, $data['P000001']['units']);
    }

    public function testConvertToDatabaseValueWithInvalidType(): void
    {
        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Expected instance of CartContentCollection");

        // When
        $this->type->convertToDatabaseValue(new \stdClass(), $this->platform);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testGetName(): void
    {
        $this->assertSame('cart_content_collection', $this->type->getName());
    }
}

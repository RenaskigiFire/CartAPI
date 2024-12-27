<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Type\Product;

use App\Domain\ValueObject\Product\ProductReference;
use App\Infrastructure\Persistence\Doctrine\Type\Product\ProductReferenceType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;

class ProductReferenceTypeTest extends TestCase
{
    private ProductReferenceType $type;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new ProductReferenceType();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testGetSQLDeclaration(): void
    {
        $column = ['length' => 255];
        $this->platform->expects($this->once())
            ->method('getStringTypeDeclarationSQL')
            ->with($column)
            ->willReturn('VARCHAR(255)');

        $this->assertSame('VARCHAR(255)', $this->type->getSQLDeclaration($column, $this->platform));
    }

    public function testConvertToPHPValue(): void
    {
        $value = 'P000123';
        $productReference = $this->type->convertToPHPValue($value, $this->platform);

        $this->assertInstanceOf(ProductReference::class, $productReference);
        $this->assertEquals('P000123', $productReference->value);
    }

    public function testConvertToDatabaseValue(): void
    {
        $productReference = new ProductReference('P000123');
        $value = $this->type->convertToDatabaseValue($productReference, $this->platform);

        $this->assertSame('P000123', $value);
    }

    public function testConvertToDatabaseValueWithString(): void
    {
        $value = $this->type->convertToDatabaseValue('P000123', $this->platform);

        $this->assertSame('P000123', $value);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testGetName(): void
    {
        $this->assertSame('product_reference', $this->type->getName());
    }
}

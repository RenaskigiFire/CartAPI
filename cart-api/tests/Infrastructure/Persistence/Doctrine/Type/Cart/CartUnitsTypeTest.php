<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Type\Cart;

use App\Domain\ValueObject\Cart\CartUnits;
use App\Infrastructure\Persistence\Doctrine\Type\Cart\CartUnitsType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;

class CartUnitsTypeTest extends TestCase
{
    private CartUnitsType $type;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new CartUnitsType();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testGetSQLDeclaration(): void
    {
        $column = ['unsigned' => true];
        $this->platform->expects($this->once())
            ->method('getIntegerTypeDeclarationSQL')
            ->with($column)
            ->willReturn('INT UNSIGNED');

        $this->assertSame('INT UNSIGNED', $this->type->getSQLDeclaration($column, $this->platform));
    }

    public function testConvertToPHPValue(): void
    {
        $value = 5;
        $cartUnits = $this->type->convertToPHPValue($value, $this->platform);

        $this->assertInstanceOf(CartUnits::class, $cartUnits);
        $this->assertEquals(5, $cartUnits->value);
    }

    public function testConvertToDatabaseValue(): void
    {
        $cartUnits = new CartUnits(10);
        $value = $this->type->convertToDatabaseValue($cartUnits, $this->platform);

        $this->assertSame(10, $value);
    }

    public function testConvertToDatabaseValueWithInt(): void
    {
        $value = $this->type->convertToDatabaseValue(15, $this->platform);

        $this->assertSame(15, $value);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testGetName(): void
    {
        $this->assertSame('cart_units', $this->type->getName());
    }
}

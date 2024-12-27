<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Type\Shared;

use App\Domain\ValueObject\Shared\UniqueId;
use App\Infrastructure\Persistence\Doctrine\Type\Shared\UniqueIdType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;

class UniqueIdTypeTest extends TestCase
{
    private UniqueIdType $type;
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        $this->type = new UniqueIdType();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testGetSQLDeclaration(): void
    {
        $column = ['length' => 36];
        $this->platform->expects($this->once())
            ->method('getStringTypeDeclarationSQL')
            ->with($column)
            ->willReturn('VARCHAR(36)');

        $this->assertSame('VARCHAR(36)', $this->type->getSQLDeclaration($column, $this->platform));
    }

    public function testConvertToPHPValue(): void
    {
        $value = '123e4567-e89b-12d3-a456-426614174000';
        $uniqueId = $this->type->convertToPHPValue($value, $this->platform);

        $this->assertInstanceOf(UniqueId::class, $uniqueId);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $uniqueId->value);
    }

    public function testConvertToDatabaseValue(): void
    {
        $uniqueId = new UniqueId('123e4567-e89b-12d3-a456-426614174000');
        $value = $this->type->convertToDatabaseValue($uniqueId, $this->platform);

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', $value);
    }

    public function testConvertToDatabaseValueWithString(): void
    {
        $value = $this->type->convertToDatabaseValue('123e4567-e89b-12d3-a456-426614174000', $this->platform);

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', $value);
    }

    public function testConvertToDatabaseValueWithNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testGetName(): void
    {
        $this->assertSame('unique_id', $this->type->getName());
    }
}

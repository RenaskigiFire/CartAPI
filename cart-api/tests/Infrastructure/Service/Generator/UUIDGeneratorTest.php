<?php

namespace App\Tests\Infrastructure\Service\Generator;

use App\Domain\ValueObject\Shared\UniqueId;
use App\Infrastructure\Service\Generator\UUIDGenerator;
use PHPUnit\Framework\TestCase;

class UUIDGeneratorTest extends TestCase
{
    public function testGenerateId()
    {
        // Given
        $uuidGenerator = new UUIDGenerator();

        // When
        $uuid = $uuidGenerator->generateId();

        // Then
        $this->assertInstanceOf(UniqueId::class, $uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid->value,
            'Generated ID is not a valid UUID'
        );
    }
}

<?php

namespace App\Tests\Domain\Collection;

use App\Domain\Collection\TypedCollection;
use PHPUnit\Framework\TestCase;

class DummyClass {}

class AnotherClass {}

class TypedCollectionTest extends TestCase
{
    public function testValidConstruction(): void
    {
        // Given
        $item1 = new DummyClass();
        $item2 = new DummyClass();

        // When
        $collection = new TypedCollection(DummyClass::class, [$item1, $item2]);

        // Then
        $this->assertCount(2, $collection);
        $this->assertSame($item1, $collection->first());
    }

    public function testInvalidConstruction(): void
    {
        // Given
        $item1 = new DummyClass();
        $item2 = new AnotherClass();

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Item must be an instance of " . DummyClass::class);

        // When
        new TypedCollection(DummyClass::class, [$item1, $item2]);
    }

    public function testAddValidItem(): void
    {
        // Given
        $item1 = new DummyClass();
        $collection = new TypedCollection(DummyClass::class);

        // When
        $collection->add('key1', $item1);

        // Then
        $this->assertCount(1, $collection);
        $this->assertSame($item1, $collection->get('key1'));
    }

    public function testAddInvalidItem(): void
    {
        // Given
        $collection = new TypedCollection(DummyClass::class);

        // Then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Item must be an instance of " . DummyClass::class);

        // When
        $collection->add('key1', new AnotherClass());
    }

    public function testJsonSerialize(): void
    {
        // Given
        $item1 = new DummyClass();
        $item2 = new DummyClass();

        $collection = new TypedCollection(DummyClass::class, [$item1, $item2]);

        // When
        $json = json_encode($collection);
        $data = json_decode($json, true);

        // Then
        $this->assertJson($json);
        $this->assertCount(2, $data);
    }
}

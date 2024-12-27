<?php

namespace App\Tests\Application\UseCase\Product\Create;

use App\Application\UseCase\Product\Create\CreateProductRequest;
use App\Application\UseCase\Product\Create\CreateProductResponse;
use App\Application\UseCase\Product\Create\CreateProductUseCase;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Tests\Domain\Entity\Product\ProductMother;
use PHPUnit\Framework\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    private CreateProductUseCase $classUnderTest;
    private ProductRepositoryInterface $productRepositoryMock;

    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->classUnderTest = new CreateProductUseCase($this->productRepositoryMock);
    }

    public function testExecuteShouldCreateProductAndReturnValidResponse(): void
    {
        // Given
        $product = ProductMother::random();

        $this->productRepositoryMock->expects($this->once())
            ->method('save');

        $request = new CreateProductRequest($product->getReference(), $product->getName());

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(CreateProductResponse::class, $response);
        $this->assertTrue($response->isValid());
        $this->assertEquals($product->getReference(),$response->productReference);
    }

    public function testExecuteShouldThrowExceptionAndReturnInvalidResponse(): void
    {
        // Given
        $this->productRepositoryMock->expects($this->never())
            ->method('save');

        $request = new CreateProductRequest('BAD-REFERENCE', 'FAIL');

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(CreateProductResponse::class, $response);
        $this->assertFalse($response->isValid());
        $this->assertNull($response->productReference);
    }
}

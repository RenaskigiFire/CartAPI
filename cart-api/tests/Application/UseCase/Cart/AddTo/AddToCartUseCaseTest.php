<?php

namespace App\Tests\Application\UseCase\Cart\AddTo;

use App\Application\UseCase\Cart\AddTo\AddToCartRequest;
use App\Application\UseCase\Cart\AddTo\AddToCartResponse;
use App\Application\UseCase\Cart\AddTo\AddToCartUseCase;
use App\Domain\Repository\CartRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Service\CartItemManagementService;
use App\Tests\Domain\Entity\Cart\CartMother;
use App\Tests\Domain\Entity\Product\ProductMother;
use App\Tests\Domain\ValueObject\Cart\CartUnitsMother;
use PHPUnit\Framework\TestCase;

class AddToCartUseCaseTest extends TestCase
{
    private AddToCartUseCase $classUnderTest;
    private CartRepositoryInterface    $cartRepositoryMock;
    private ProductRepositoryInterface $productRepositoryMock;
    private CartItemManagementService  $cartItemManagementServiceMock;

    protected function setUp(): void
    {
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $this->cartItemManagementServiceMock = $this->createMock(CartItemManagementService::class);
        $this->classUnderTest = new AddToCartUseCase(
            cartRepository: $this->cartRepositoryMock,
            productRepository: $this->productRepositoryMock,
            cartItemManagementService: $this->cartItemManagementServiceMock
        );
    }

    public function testExecuteShouldReturnValidResponse()
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $quantity = CartUnitsMother::random();
        $request = new AddToCartRequest(
            cartId: $cart->getId(),
            productReference: $product->getReference(),
            quantity: $quantity->value,
        );

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->with($cart->getId())
            ->willReturn($cart);
        $this->productRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->with($product->getReference())
            ->willReturn($product);
        $this->cartItemManagementServiceMock->expects($this->once())
            ->method('addProductToCart')
            ->with($cart, $product, $quantity->value);
        $this->cartRepositoryMock->expects($this->once())
            ->method('save')
            ->with($cart);

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(AddToCartResponse::class, $response);
        $this->assertTrue($response->isValid());
        $this->assertEquals($cart,$response->cart);
    }

    public function testExecuteShouldReturnInvalidResponse()
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $quantity = CartUnitsMother::random();
        $request = new AddToCartRequest(
            cartId: $cart->getId(),
            productReference: $product->getReference(),
            quantity: $quantity->value,
        );

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->with($cart->getId())
            ->willThrowException(new \Exception());

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(AddToCartResponse::class, $response);
        $this->assertFalse($response->isValid());
        $this->assertNull($response->cart);
    }
}

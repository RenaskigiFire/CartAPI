<?php

namespace App\Tests\Application\UseCase\Cart\Purchase;

use App\Application\UseCase\Cart\Purchase\PurchaseCartResponse;
use App\Application\UseCase\Cart\Purchase\PurchaseCartUseCase;
use App\Domain\Repository\CartRepositoryInterface;
use App\Tests\Domain\Entity\Cart\CartMother;
use PHPUnit\Framework\TestCase;

class PurchaseCartUseCaseTest extends TestCase
{
    private PurchaseCartUseCase $classUnderTest;
    private CartRepositoryInterface $cartRepositoryMock;

    protected function setUp(): void
    {
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->classUnderTest = new PurchaseCartUseCase($this->cartRepositoryMock);
    }

    public function testExecuteShouldReturnValidResponse()
    {
        // Given
        $cart = CartMother::random();

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->willReturn($cart);

        $this->cartRepositoryMock->expects($this->once())
            ->method('delete');

        // When
        $response = $this->classUnderTest->execute($cart->getId());

        // Then
        $this->assertInstanceOf(PurchaseCartResponse::class, $response);
        $this->assertTrue($response->isValid());
    }

    public function testExecuteShouldReturnInvalidResponse()
    {
        // Given
        $cart = CartMother::random();

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->willThrowException(new \Exception());

        $this->cartRepositoryMock->expects($this->never())
            ->method('delete');

        // When
        $response = $this->classUnderTest->execute($cart->getId());

        // Then
        $this->assertInstanceOf(PurchaseCartResponse::class, $response);
        $this->assertFalse($response->isValid());
    }
}

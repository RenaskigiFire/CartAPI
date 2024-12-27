<?php

namespace App\Tests\Application\UseCase\Cart\Get;

use App\Application\UseCase\Cart\Get\GetCartResponse;
use App\Application\UseCase\Cart\Get\GetCartUseCase;
use App\Domain\Repository\CartRepositoryInterface;
use App\Tests\Domain\Entity\Cart\CartMother;
use PHPUnit\Framework\TestCase;

class GetCartUseCaseTest extends TestCase
{
    private GetCartUseCase $classUnderTest;
    private CartRepositoryInterface $cartRepositoryMock;

    protected function setUp(): void
    {
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->classUnderTest = new GetCartUseCase(
            $this->cartRepositoryMock
        );
    }

    public function testExecuteShouldReturnValidResponse()
    {
        // Given
        $cart = CartMother::random();

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->with($cart->getId())
            ->willReturn($cart);


        // When
        $response = $this->classUnderTest->execute($cart->getId());

        // Then
        $this->assertInstanceOf(GetCartResponse::class, $response);
        $this->assertTrue($response->isValid());
        $this->assertEquals($cart,$response->cart);
    }

    public function testExecuteShouldReturnInvalidResponse()
    {
        // Given
        $cart = CartMother::random();

        $this->cartRepositoryMock->expects($this->once())
            ->method('findOrFail')
            ->with($cart->getId())
            ->willThrowException(new \Exception());

        // When
        $response = $this->classUnderTest->execute($cart->getId());

        // Then
        $this->assertInstanceOf(GetCartResponse::class, $response);
        $this->assertFalse($response->isValid());
        $this->assertNull($response->cart);
    }
}

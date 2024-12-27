<?php

namespace App\Tests\Application\UseCase\Cart\Create;

use App\Application\UseCase\Cart\Create\CreateCartRequest;
use App\Application\UseCase\Cart\Create\CreateCartResponse;
use App\Application\UseCase\Cart\Create\CreateCartUseCase;
use App\Domain\Repository\CartRepositoryInterface;
use App\Domain\Service\Builder\CartBuilder;
use App\Tests\Domain\Entity\Cart\CartMother;
use App\Tests\Domain\ValueObject\Shared\UniqueIdMother;
use PHPUnit\Framework\TestCase;

class CreateCartUseCaseTest extends TestCase
{

    private CreateCartUseCase $classUnderTest;
    private CartBuilder $cartBuilderMock;
    private CartRepositoryInterface $cartRepositoryMock;

    protected function setUp(): void
    {
        $this->cartBuilderMock = $this->createMock(CartBuilder::class);
        $this->cartRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->classUnderTest = new CreateCartUseCase($this->cartBuilderMock, $this->cartRepositoryMock);
    }

    public function testExecuteShouldReturnValidResponse()
    {
        // Given
        $cartId = UniqueIdMother::random();
        $sessionId = UniqueIdMother::random();
        $userId = UniqueIdMother::random();
        $cart = CartMother::apply(
            $cartId,
            $sessionId,
            $userId
        );

        $this->cartBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($cart);

        $this->cartRepositoryMock->expects($this->once())
            ->method('save');

        $request = new CreateCartRequest(sessionId: $sessionId,userId: $userId);

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(CreateCartResponse::class, $response);
        $this->assertTrue($response->isValid());
        $this->assertEquals($cartId->value,$response->cartId);
    }

    public function testExecuteShouldReturnInvalidResponse()
    {
        // Given
        $cartId = UniqueIdMother::random();
        $sessionId = UniqueIdMother::random();
        $userId = UniqueIdMother::random();
        $cart = CartMother::apply(
            $cartId,
            $sessionId,
            $userId
        );

        $this->cartBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($cart);

        $this->cartRepositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception());

        $request = new CreateCartRequest(sessionId: $sessionId,userId: $userId);

        // When
        $response = $this->classUnderTest->execute($request);

        // Then
        $this->assertInstanceOf(CreateCartResponse::class, $response);
        $this->assertFalse($response->isValid());
        $this->assertNull($response->cartId);
    }
}

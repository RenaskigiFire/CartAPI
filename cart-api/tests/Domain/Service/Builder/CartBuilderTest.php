<?php

namespace App\Tests\Domain\Service\Builder;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Service\Builder\CartBuilder;
use App\Domain\Service\Generator\IdentityGeneratorInterface;
use App\Tests\Domain\ValueObject\Shared\UniqueIdMother;
use PHPUnit\Framework\TestCase;

class CartBuilderTest extends TestCase
{
    private CartBuilder $classUnderTest;
    private IdentityGeneratorInterface $identityGeneratorMock;

    protected function setUp(): void
    {
        $this->identityGeneratorMock = $this->createMock(IdentityGeneratorInterface::class);

        $this->classUnderTest = new CartBuilder($this->identityGeneratorMock);
    }

    public function testBuildShouldReturnValidCart(): void
    {
        // Given
        $sessionId = UniqueIdMother::random();
        $userId    = UniqueIdMother::random();
        $id        = UniqueIdMother::random();

        $this->identityGeneratorMock->expects($this->once())
            ->method('generateId')
            ->willReturn($id);

        // When
        $cart = $this->classUnderTest->build($sessionId->value, $userId->value);

        // Then
        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals($cart->getId(), $id->value);
        $this->assertEquals($cart->getSessionId(), $sessionId->value);
        $this->assertEquals($cart->getUserId(), $userId->value);
    }
}

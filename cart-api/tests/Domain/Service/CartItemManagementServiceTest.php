<?php

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Cart\CartContent;
use App\Domain\Service\CartItemManagementService;
use App\Tests\Domain\Entity\Cart\CartContentMother;
use App\Tests\Domain\Entity\Cart\CartMother;
use App\Tests\Domain\Entity\Product\ProductMother;
use App\Tests\Domain\ValueObject\Cart\CartUnitsMother;
use PHPUnit\Framework\TestCase;

class CartItemManagementServiceTest extends TestCase
{
    private CartItemManagementService $classUnderTest;

    protected function setUp(): void
    {
        $this->classUnderTest = new CartItemManagementService();
    }

    public function testAddProductToCartWhenCartIsEmpty(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $cartUnits = CartUnitsMother::random();
        $cartContent = CartContentMother::apply(
            $product,
            $cartUnits,
        );

        // When
        $this->classUnderTest->addProductToCart($cart, $product, $cartUnits->value);

        // Then
        $this->assertEquals($cart->getProductCount(),$cartUnits->value);
        $this->assertEquals(1,$cart->getCartContents()->count());
        $this->assertEquals($cart->getCartContents()->first(),$cartContent);
    }

    public function testAddProductToCartWhenCartIsNotEmpty(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $cartUnits = CartUnitsMother::random();
        $newCartUnits = CartUnitsMother::random();
        $totalUnits = $cartUnits->value + $newCartUnits->value;

        $this->classUnderTest->addProductToCart($cart, $product, $cartUnits->value);

        // When
        $this->classUnderTest->addProductToCart($cart, $product, $newCartUnits->value);

        // Then
        $this->assertEquals($cart->getProductCount(),$totalUnits);
        $this->assertEquals(1,$cart->getCartContents()->count());
        /** @var CartContent $cartContentInCart */
        $cartContentInCart = $cart->getCartContents()->first();
        $this->assertEquals($cartContentInCart->getUnits(),$totalUnits);
        $this->assertEquals($cartContentInCart->getProduct(),$product);
    }

    public function testAddProductToCartShouldThrowExceptionWhenUnitsAreNegative(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();


        // Then
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cart unit must be greater than 1');

        // When
        $this->classUnderTest->addProductToCart($cart, $product, -6);
    }

    public function testRemoveAllProductUnitsFromCartWhenCartIsNotEmpty(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $cartUnits = CartUnitsMother::random();

        $this->classUnderTest->addProductToCart($cart, $product, $cartUnits->value);

        // When
        $this->classUnderTest->removeProductFromCart($cart, $product, $cartUnits->value);

        // Then
        $this->assertEquals(0,$cart->getProductCount());
        $this->assertEquals(0,$cart->getCartContents()->count());
    }

    public function testRemoveSomeProductUnitsFromCartWhenCartIsNotEmpty(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $cartUnits = CartUnitsMother::apply(5);
        $newCartUnits = CartUnitsMother::apply(4);
        $totalUnits = $cartUnits->value - $newCartUnits->value;

        $this->classUnderTest->addProductToCart($cart, $product, $cartUnits->value);

        // When
        $this->classUnderTest->removeProductFromCart($cart, $product, $newCartUnits->value);

        // Then
        $this->assertEquals($cart->getProductCount(),$totalUnits);
        $this->assertEquals(1,$cart->getCartContents()->count());
        /** @var CartContent $cartContentInCart */
        $cartContentInCart = $cart->getCartContents()->first();
        $this->assertEquals($cartContentInCart->getUnits(),$totalUnits);
        $this->assertEquals($cartContentInCart->getProduct(),$product);
    }

    public function testRemoveProductFromCartWhenCartIsEmpty(): void
    {
        // Given
        $cart = CartMother::random();
        $product = ProductMother::random();
        $cartUnits = CartUnitsMother::random();

        // When
        $this->classUnderTest->removeProductFromCart($cart, $product, $cartUnits->value);

        // Then
        $this->assertEquals(0,$cart->getProductCount());
        $this->assertEquals(0,$cart->getCartContents()->count());
    }
}

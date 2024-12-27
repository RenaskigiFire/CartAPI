<?php

namespace App\Tests\Infrastructure\Service\Transformer;

use App\Infrastructure\Service\Transformer\CartDataTransformer;
use App\Tests\Domain\Entity\Cart\CartMother;
use PHPUnit\Framework\TestCase;

class CartDataTransformerTest extends TestCase
{

    public function testTransformCartToJson(): void
    {
        // Given
        $cart = CartMother::randomWithContent();
        $transformer = new CartDataTransformer();

        // When
        $result = $transformer->transform($cart);

        // Then
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('session_id', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('product_count', $result);
        $this->assertArrayHasKey('contents', $result);

        $this->assertEquals($cart->getId(), $result['id']);
        $this->assertEquals($cart->getSessionId(), $result['session_id']);
        $this->assertEquals($cart->getUserId(), $result['user_id']);
        $this->assertEquals($cart->getProductCount(), $result['product_count']);

        foreach ($result['contents'] as $index => $content) {
            $cartContent = $cart->getCartContents()->findByProductReference($content['product_reference']);
            $this->assertEquals($cartContent->getProduct()->getReference(), $content['product_reference']);
            $this->assertEquals($cartContent->getProduct()->getName(), $content['product_name']);
            $this->assertEquals($cartContent->getUnits(), $content['units']);
        }
    }
}

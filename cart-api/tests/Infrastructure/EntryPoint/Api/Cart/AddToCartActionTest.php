<?php

namespace App\Tests\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\AddTo\AddToCartResponse;
use App\Application\UseCase\Cart\AddTo\AddToCartUseCase;
use App\Infrastructure\EntryPoint\Api\Cart\AddToCartAction;
use App\Infrastructure\Service\Transformer\CartDataTransformer;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use App\Tests\Domain\Entity\Cart\CartMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddToCartActionTest extends TestCase
{
    private AddToCartUseCase $addToCartUseCaseMock;
    private HttpRequestContentValidator $httpRequestContentValidatorMock;
    private CartDataTransformer $cartDataTransformerMock;
    private AddToCartAction $classUnderTest;

    protected function setUp(): void
    {
        $this->httpRequestContentValidatorMock = $this->createMock(HttpRequestContentValidator::class);
        $this->cartDataTransformerMock = $this->createMock(CartDataTransformer::class);
        $this->addToCartUseCaseMock = $this->createMock(AddToCartUseCase::class);
        $this->classUnderTest = new AddToCartAction(
            httpRequestContentValidator: $this->httpRequestContentValidatorMock,
            addToCartUseCase           : $this->addToCartUseCaseMock,
            cartDataTransformer        : $this->cartDataTransformerMock,
        );
    }

    public function testInvokeValidRequest(): void
    {
        // Given
        $cart = CartMother::random();
        $transformedCart = [
            'id' => '123e4567-e89b-12d3-a456-426614174000',
            'session_id' => $cart->getSessionId(),
            'user_id' => $cart->getUserId(),
            'product_count' => 2,
            'contents' => [[
                'id' => '321e4567-e89b-12d3-a456-426614174000',
                'product' => [
                    'reference' => 'P123456',
                    'name' => 'Test',
                ],
                'units' => 2,
                'cart_id' => '123e4567-e89b-12d3-a456-426614174000',
            ]]
        ];

        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->addToCartUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(AddToCartResponse::createValidResponse($cart));

        $this->cartDataTransformerMock
            ->expects($this->once())
            ->method('transform')
            ->willReturn($transformedCart);

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                   'cart_id' => '123e4567-e89b-12d3-a456-426614174000',
                                   'product_reference' => 'P123456',
                                   'quantity' => 2,
                                               ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Product added to cart', $responseData['message']);
        $this->assertEquals($transformedCart, $responseData['cart']);
    }

    public function testInvokeInvalidRequestOnBadRequest(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(false);

        $this->addToCartUseCaseMock
            ->expects($this->never())
            ->method('execute');

        $this->cartDataTransformerMock
            ->expects($this->never())
            ->method('transform');

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'invalid' => 'value'
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Bad Request', $responseData['message']);
        $this->assertNull($responseData['cart']);
    }

    public function testInvokeInvalidRequestOnThrowException(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new \Exception('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR));

        $this->addToCartUseCaseMock
            ->expects($this->never())
            ->method('execute');

        $this->cartDataTransformerMock
            ->expects($this->never())
            ->method('transform');

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'invalid' => 'value'
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertNull($responseData['cart']);
    }

    public function testInvokeInvalidRequestByUseCase(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->addToCartUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(AddToCartResponse::createInvalidResponse('Server Error'));

        $this->cartDataTransformerMock
            ->expects($this->never())
            ->method('transform');

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => '123e4567-e89b-12d3-a456-426614174000',
                                               'product_reference' => 'P123456',
                                               'quantity' => 2,
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('Server Error', $responseData['message']);
        $this->assertNull($responseData['cart']);
    }
}

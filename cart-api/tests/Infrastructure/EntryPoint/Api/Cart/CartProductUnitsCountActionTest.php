<?php

namespace App\Tests\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\Get\GetCartResponse;
use App\Application\UseCase\Cart\Get\GetCartUseCase;
use App\Infrastructure\EntryPoint\Api\Cart\CartProductUnitsCountAction;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use App\Tests\Domain\Entity\Cart\CartMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartProductUnitsCountActionTest extends TestCase
{
    private GetCartUseCase $getCartUseCaseMock;
    private HttpRequestContentValidator $httpRequestContentValidatorMock;
    private CartProductUnitsCountAction $classUnderTest;

    protected function setUp(): void
    {
        $this->getCartUseCaseMock = $this->createMock(GetCartUseCase::class);
        $this->httpRequestContentValidatorMock = $this->createMock(HttpRequestContentValidator::class);
        $this->classUnderTest = new CartProductUnitsCountAction(
            httpRequestContentValidator: $this->httpRequestContentValidatorMock,
            getCartUseCase: $this->getCartUseCaseMock
        );
    }

    public function testInvokeValidRequest(): void
    {
        // Given
        $cart = CartMother::random();

        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->getCartUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(GetCartResponse::createValidResponse($cart));

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => '123e4567-e89b-12d3-a456-426614174000',
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Cart found', $responseData['message']);
        $this->assertEquals($cart->getProductCount(), $responseData['cart_product_count']);
    }

    public function testInvokeInvalidRequestOnBadRequest(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(false);

        $this->getCartUseCaseMock
            ->expects($this->never())
            ->method('execute');

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
        $this->assertNull($responseData['cart_product_count']);
    }

    public function testInvokeInvalidRequestOnThrowException(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new \Exception('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR));

        $this->getCartUseCaseMock
            ->expects($this->never())
            ->method('execute');

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
        $this->assertNull($responseData['cart_product_count']);
    }

    public function testInvokeInvalidRequestByUseCase(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->getCartUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(GetCartResponse::createInvalidResponse('Server Error'));

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => '123e4567-e89b-12d3-a456-426614174000'
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('Server Error', $responseData['message']);
        $this->assertNull($responseData['cart_product_count']);
    }
}

<?php

namespace App\Tests\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\Purchase\PurchaseCartResponse;
use App\Application\UseCase\Cart\Purchase\PurchaseCartUseCase;
use App\Infrastructure\EntryPoint\Api\Cart\PurchaseCartAction;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use App\Tests\Domain\ValueObject\Shared\UniqueIdMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PurchaseCartActionTest extends TestCase
{
    private PurchaseCartUseCase $useCaseMock;
    private HttpRequestContentValidator $httpRequestContentValidatorMock;
    private PurchaseCartAction $classUnderTest;

    protected function setUp(): void
    {
        $this->useCaseMock = $this->createMock(PurchaseCartUseCase::class);
        $this->httpRequestContentValidatorMock = $this->createMock(HttpRequestContentValidator::class);
        $this->classUnderTest = new PurchaseCartAction($this->httpRequestContentValidatorMock, $this->useCaseMock);
    }

    public function testInvokeValidRequest(): void
    {
        // Given
        $cartId = UniqueIdMother::random();

        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willReturn(PurchaseCartResponse::createValidResponse());

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => $cartId->value
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Cart purchased successfully.', $responseData['message']);
    }

    public function testInvokeInvalidRequestOnBadRequest(): void
    {
        // Given
        $cartId = UniqueIdMother::random();

        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(false);

        $this->useCaseMock->expects($this->never())
            ->method('execute');

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => $cartId->value
                                           ]));
        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('Bad Request', $responseData['message']);
    }

    public function testInvokeInvalidRequestOnThrowException(): void
    {
        // Given
        $this->httpRequestContentValidatorMock
            ->expects($this->once())
            ->method('validate')
            ->willThrowException(new \Exception('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR));

        $this->useCaseMock->expects($this->never())
            ->method('execute');

        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'cart_id' => 'a'
                                           ]));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}

<?php

namespace App\Tests\Infrastructure\EntryPoint\Api\Cart;

use App\Application\UseCase\Cart\Create\CreateCartRequest;
use App\Application\UseCase\Cart\Create\CreateCartResponse;
use App\Application\UseCase\Cart\Create\CreateCartUseCase;
use App\Infrastructure\EntryPoint\Api\Cart\CreateCartAction;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateCartActionTest extends TestCase
{
    private HttpRequestContentValidator $validatorMock;
    private CreateCartUseCase $useCaseMock;
    private CreateCartAction  $classUnderTest;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(HttpRequestContentValidator::class);
        $this->useCaseMock = $this->createMock(CreateCartUseCase::class);
        $this->classUnderTest = new CreateCartAction($this->validatorMock, $this->useCaseMock);
    }

    public function testInvokeReturnsBadRequestWhenValidationFails(): void
    {
        // Given
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                   'user_id' => '5588958d-74e0-4370-9c76-176212138a67'
                                           ]));

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(false);

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Bad Request', 'cart_id' => null]),
            $response->getContent()
        );
    }

    public function testInvokeReturnsSuccessfulResponseWhenUseCaseSucceeds(): void
    {
        // Given
        $cartId = 'cfee1c89-e450-4cef-a42d-5d2f74dd2813';
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                   'session_id' => '6668958d-74e0-4370-9c76-176212138a67',
                                   'user_id' => '5588958d-74e0-4370-9c76-176212138a67'
                                           ]));
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willReturn(CreateCartResponse::createValidResponse($cartId));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Success on creating new cart', 'cart_id' => $cartId]),
            $response->getContent()
        );
    }

    public function testInvokeReturnsInvalidResponseWhenUseCaseResponseInvalidResponse(): void
    {
        // Given
        $cartId = 'cfee1c89-e450-4cef-a42d-5d2f74dd2813';
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'session_id' => '6668958d-74e0-4370-9c76-176212138a67',
                                               'user_id' => '5588958d-74e0-4370-9c76-176212138a67'
                                           ]));
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willReturn(CreateCartResponse::createInvalidResponse('Server error'));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Server error', 'cart_id' => null]),
            $response->getContent()
        );
    }

    public function testInvokeHandlesException(): void
    {
        // Given
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                   'session_id' => 'session-12345',
                                   'user_id' => 'user-67890'
                                           ]));

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new \Exception('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Internal Server Error', 'cart_id' => null]),
            $response->getContent()
        );
    }
}


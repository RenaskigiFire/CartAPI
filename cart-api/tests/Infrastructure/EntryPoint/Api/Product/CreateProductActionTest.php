<?php

namespace App\Tests\Infrastructure\EntryPoint\Api\Product;

use App\Application\UseCase\Product\Create\CreateProductResponse;
use App\Application\UseCase\Product\Create\CreateProductUseCase;
use App\Infrastructure\EntryPoint\Api\Product\CreateProductAction;
use App\Infrastructure\Service\Validator\HttpRequestContentValidator;
use App\Tests\Domain\Entity\Product\ProductMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateProductActionTest extends TestCase
{
    private HttpRequestContentValidator $validatorMock;
    private CreateProductUseCase $useCaseMock;
    private CreateProductAction  $classUnderTest;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(HttpRequestContentValidator::class);
        $this->useCaseMock = $this->createMock(CreateProductUseCase::class);
        $this->classUnderTest = new CreateProductAction($this->validatorMock, $this->useCaseMock);
    }

    public function testInvokeReturnsBadRequestWhenValidationFails(): void
    {
        // Given
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'product_reference' => 'P123456'
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
            json_encode(['message' => 'Bad Request', 'product_reference' => null]),
            $response->getContent()
        );
    }

    public function testInvokeReturnsSuccessfulResponseWhenUseCaseSucceeds(): void
    {
        // Given
        $product = ProductMother::random();
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'product_reference' => $product->getReference(),
                                               'name' => $product->getName(),
                                           ]));
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willReturn(CreateProductResponse::createValidResponse($product->getReference()));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Success on creating new product', 'product_reference' => $product->getReference()]),
            $response->getContent()
        );
    }

    public function testInvokeReturnsInvalidResponseWhenUseCaseResponseInvalidResponse(): void
    {
        // Given
        $product = ProductMother::random();
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'product_reference' => $product->getReference(),
                                               'name' => $product->getName(),
                                           ]));
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->useCaseMock->expects($this->once())
            ->method('execute')
            ->willReturn(CreateProductResponse::createInvalidResponse('Server error'));

        // When
        $response = $this->classUnderTest->__invoke($request);

        // Then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Server error', 'product_reference' => null]),
            $response->getContent()
        );
    }

    public function testInvokeHandlesException(): void
    {
        // Given
        $product = ProductMother::random();
        $request = new Request([], [], [], [], [], [],
                               json_encode([
                                               'product_reference' => $product->getReference(),
                                               'name' => $product->getName(),
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
            json_encode(['message' => 'Internal Server Error', 'product_reference' => null]),
            $response->getContent()
        );
    }
}

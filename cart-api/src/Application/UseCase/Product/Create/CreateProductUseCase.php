<?php

namespace App\Application\UseCase\Product\Create;

use App\Domain\Entity\Product\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\Product\ProductReference;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    )
    {
    }

    public function execute(CreateProductRequest $request): CreateProductResponse
    {
        try {
            $product = new Product(
                reference: new ProductReference($request->productReference),
                name: $request->name,
            );
            $this->productRepository->save($product);

            return CreateProductResponse::createValidResponse(productReference: $product->getReference());
        } catch (\Exception $exception) {
            return CreateProductResponse::createInvalidResponse($exception->getMessage());
        }
    }
}
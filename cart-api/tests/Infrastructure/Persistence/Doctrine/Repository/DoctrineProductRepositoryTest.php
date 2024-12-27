<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Repository;

use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineProductRepository;
use App\Tests\Domain\Entity\Product\ProductMother;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineProductRepositoryTest extends KernelTestCase
{
    private DoctrineProductRepository $classUnderTest;
    private EntityManagerInterface $entityManager;
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->classUnderTest = new DoctrineProductRepository($this->entityManager);
    }

    public function testSaveShouldSaveProductOnBD(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');

        // Given
        $product = ProductMother::random();

        // When
        $this->classUnderTest->save($product);

        // Given
        $bdProduct = $this->classUnderTest->findOrFail($product->getReference());
        $this->assertEquals($product, $bdProduct);

        // TearDown
        $this->entityManager->getConnection()->rollback();
    }

    public function testFindOrFailShouldFail(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');

        // Given
        $product = ProductMother::random();

        // Then
        $this->expectException(EntityNotFoundException::class);

        // When
        $bdProduct = $this->classUnderTest->findOrFail($product->getReference());

        // TearDown
        $this->entityManager->getConnection()->rollback();
    }
}

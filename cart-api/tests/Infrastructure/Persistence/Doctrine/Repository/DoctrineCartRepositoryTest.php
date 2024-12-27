<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine\Repository;

use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineCartRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineProductRepository;
use App\Tests\Domain\Entity\Cart\CartMother;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineCartRepositoryTest extends KernelTestCase
{
    private DoctrineCartRepository $classUnderTest;
    private DoctrineProductRepository $productRepository;
    private EntityManagerInterface $entityManager;
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->productRepository = new DoctrineProductRepository($this->entityManager);
        $this->classUnderTest = new DoctrineCartRepository($this->entityManager);
    }

    public function testSaveShouldSaveCartOnBD(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM carts');

        // Given
        $cart = CartMother::randomWithContent($this->productRepository);

        // When
        $this->classUnderTest->save($cart);

        // Then
        $bdCart = $this->classUnderTest->findOrFail($cart->getId());
        $this->assertEquals($cart, $bdCart);

        // TearDown
        $this->entityManager->getConnection()->rollback();
    }

    public function testFindOrFailShouldThrowException(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM carts');

        // Given
        $cart = CartMother::randomWithContent($this->productRepository);

        // Then
        $this->expectException(EntityNotFoundException::class);

        // When
        $bdCart = $this->classUnderTest->findOrFail($cart->getId());

        // TearDown
        $this->entityManager->getConnection()->rollback();
    }

    public function testDeleteShouldDeleteCartOnBD(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM products');
        $this->entityManager->getConnection()->executeStatement('DELETE FROM carts');

        // Given
        $cart = CartMother::randomWithContent($this->productRepository);
        $this->classUnderTest->save($cart);

        // When
        $this->classUnderTest->delete($cart);

        // Then
        $this->expectException(EntityNotFoundException::class);
        $bdCart = $this->classUnderTest->findOrFail($cart->getId());

        // TearDown
        $this->entityManager->getConnection()->rollback();
    }
}

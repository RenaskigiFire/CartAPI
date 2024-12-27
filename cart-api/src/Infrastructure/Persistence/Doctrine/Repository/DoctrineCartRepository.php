<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Cart\Cart;
use App\Domain\Repository\CartRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class DoctrineCartRepository implements CartRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findOrFail(string $uniqueId): Cart
    {
        $cart = $this->entityManager->getRepository(Cart::class)->find($uniqueId);

        if ($cart === null) {
            throw new EntityNotFoundException("Cart with reference {$uniqueId} not found.");
        }

        return $cart;
    }

    public function save(Cart $cart): void
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function delete(Cart $cart): void
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }
}

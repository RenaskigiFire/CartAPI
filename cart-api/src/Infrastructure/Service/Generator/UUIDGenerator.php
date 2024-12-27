<?php

namespace App\Infrastructure\Service\Generator;

use App\Domain\Service\Generator\IdentityGeneratorInterface;
use App\Domain\ValueObject\Shared\UniqueId;
use Symfony\Component\Uid\Uuid;


class UUIDGenerator implements IdentityGeneratorInterface
{

    public function generateId(): UniqueId
    {
        return new UniqueId(Uuid::v4()->toString());
    }
}

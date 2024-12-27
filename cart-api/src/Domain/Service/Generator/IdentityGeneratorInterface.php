<?php
namespace App\Domain\Service\Generator;

use App\Domain\ValueObject\Shared\UniqueId;

interface IdentityGeneratorInterface
{
    public function generateId(): UniqueId;
}

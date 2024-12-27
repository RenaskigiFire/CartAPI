<?php

namespace App\Domain\ValueObject\Shared;

readonly class UniqueId
{
    public function __construct(
        public string $value
    )
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

<?php

namespace App\Domain\Collection;

use ArrayIterator;
use Traversable;

class TypedCollection implements \IteratorAggregate, \JsonSerializable
{
    public function __construct(
        private readonly string $type,
        protected array $items = []
    ) {
        foreach ($items as $item) {
            $this->validateType($item);
        }
    }

    private function validateType(mixed $item): void
    {
        if (!$item instanceof $this->type) {
            throw new \InvalidArgumentException("Item must be an instance of {$this->type}");
        }
    }

    public function add(string $key, mixed $item): void
    {
        $this->validateType($item);
        $this->items[$key] = $item;
    }

    public function remove(string $key): void
    {
        unset($this->items[$key]);
    }

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function first(): mixed
    {
        return reset($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }
}

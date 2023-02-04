<?php

declare(strict_types=1);

namespace App\Application\Category\GetAll;

final class CategoriesCollection
{
    private readonly array $items;

    public function __construct(CategoryDTO ...$items)
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}

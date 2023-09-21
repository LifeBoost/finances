<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;

interface CategoryRepository
{
    public function store(Category $category): void;

    public function existsByName(string $name, CategoryType $type): bool;

    /**
     * @throws NotFoundException
     */
    public function getById(Id $id): Category;

    public function save(Category $category): void;

    /**
     * @throws NotFoundException
     */
    public function delete(Id $id): void;
}

<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\SharedKernel\Exception\NotFoundException;
use Ramsey\Uuid\UuidInterface;

interface CategoryRepository
{
    public function store(Category $category): void;

    public function existsByName(string $name, CategoryType $type): bool;

    /**
     * @throws NotFoundException
     */
    public function getById(CategoryId $id): Category;

    public function save(Category $category): void;

    /**
     * @throws NotFoundException
     */
    public function delete(CategoryId $id): void;
}

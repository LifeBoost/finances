<?php

declare(strict_types=1);

namespace App\Application\Category\GetAll;

final class CategoryDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $name,
        public readonly string $icon,
    ){}
}

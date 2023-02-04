<?php

declare(strict_types=1);

namespace App\Application\Category\Update;

final class UpdateCategoryCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $icon,
    ){}
}

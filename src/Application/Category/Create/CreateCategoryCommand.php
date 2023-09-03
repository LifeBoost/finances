<?php

declare(strict_types=1);

namespace App\Application\Category\Create;

final class CreateCategoryCommand
{
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $icon,
    ) {
    }
}

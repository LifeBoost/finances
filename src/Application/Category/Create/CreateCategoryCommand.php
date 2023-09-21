<?php

declare(strict_types=1);

namespace App\Application\Category\Create;

final readonly class CreateCategoryCommand
{
    public function __construct(
        public string $type,
        public string $name,
        public string $icon,
    ) {}
}

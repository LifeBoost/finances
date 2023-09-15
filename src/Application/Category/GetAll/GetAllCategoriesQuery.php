<?php

declare(strict_types=1);

namespace App\Application\Category\GetAll;

final readonly class GetAllCategoriesQuery
{
    public function __construct(
        public ?string $filterType = null,
    ) {
    }
}

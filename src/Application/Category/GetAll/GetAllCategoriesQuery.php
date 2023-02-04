<?php

declare(strict_types=1);

namespace App\Application\Category\GetAll;

final class GetAllCategoriesQuery
{
    public function __construct(
        public readonly ?string $filterType,
    ){}
}

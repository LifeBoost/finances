<?php

declare(strict_types=1);

namespace App\Application\Category\GetOneById;

final readonly class GetOneCategoryByIdQuery
{
    public function __construct(public string $id) {}
}

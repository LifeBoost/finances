<?php

declare(strict_types=1);

namespace App\Application\Category\GetOneById;

final class GetOneCategoryByIdQuery
{
    public function __construct(public readonly string $id){}
}

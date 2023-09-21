<?php

declare(strict_types=1);

namespace App\Application\Transaction\GetOneById;

final class GetOneTransactionByIdQuery
{
    public function __construct(public readonly string $id) {}
}

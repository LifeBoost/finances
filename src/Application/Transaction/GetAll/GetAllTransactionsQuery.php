<?php

declare(strict_types=1);

namespace App\Application\Transaction\GetAll;

final class GetAllTransactionsQuery
{
    public function __construct(
        public readonly ?string $dateFrom,
        public readonly ?string $dateTo
    ) {
    }
}

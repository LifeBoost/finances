<?php

declare(strict_types=1);

namespace App\UI\API\Request\Transaction;

final readonly class CreateTransactionRequest
{
    public function __construct(
        public string $type,
        public string $sourceWalletId,
        public ?string $targetWalletId,
        public ?string $categoryId,
        public string $date,
        public string $description,
        public int $amount,
    ) {
    }
}

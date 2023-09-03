<?php

declare(strict_types=1);

namespace App\Application\Transaction\Update;

final class UpdateTransactionCommand
{
    public function __construct(
        public string $id,
        public readonly string $type,
        public readonly string $sourceWalletId,
        public readonly ?string $targetWalletId,
        public readonly ?string $categoryId,
        public readonly string $date,
        public readonly string $description,
        public readonly int $amount,
    ) {
    }
}

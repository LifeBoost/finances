<?php

declare(strict_types=1);

namespace App\Application\Transaction\Create;

final class CreateTransactionCommand
{
    public function __construct(
        public readonly string $type,
        public readonly string $sourceWalletId,
        public readonly ?string $targetWalletId,
        public readonly ?string $categoryId,
        public readonly string $date,
        public readonly string $description,
        public readonly int $amount,
    ){}
}

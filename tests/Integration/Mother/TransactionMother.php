<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Mother;

use App\Domain\Transaction\TransactionType;

final readonly class TransactionMother extends AbstractMother
{
    public static function prepareJsonData(
        string $type = TransactionType::INCOME->value,
        string $sourceWalletId = '',
        ?string $targetWalletId = '',
        ?string $categoryId = '',
        string $date = '',
        string $description = '',
        int $amount = 0,
    ): array {
        return [
            'type' => $type,
            'sourceWalletId' => $sourceWalletId,
            'targetWalletId' => $targetWalletId,
            'categoryId' => $categoryId,
            'date' => $date,
            'description' => $description,
            'amount' => $amount,
        ];
    }

    public static function getUrlPattern(): string
    {
        return 'api/v1/transactions';
    }
}

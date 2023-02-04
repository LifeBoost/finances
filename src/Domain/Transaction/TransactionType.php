<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case TRANSFER = 'transfer';
}

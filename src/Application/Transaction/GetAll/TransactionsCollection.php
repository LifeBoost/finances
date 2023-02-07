<?php

declare(strict_types=1);

namespace App\Application\Transaction\GetAll;

final class TransactionsCollection
{
    private readonly array $items;

    public function __construct(TransactionDTO ...$items)
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}

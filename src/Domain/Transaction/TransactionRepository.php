<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\SharedKernel\Exception\NotFoundException;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function getById(TransactionId $id): Transaction;

    public function save(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function delete(TransactionId $id): void;
}

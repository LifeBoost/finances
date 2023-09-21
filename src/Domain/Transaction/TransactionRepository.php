<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function getById(Id $id): Transaction;

    public function save(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function delete(Id $id): void;
}

<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\SharedKernel\Exception\NotFoundException;
use Ramsey\Uuid\UuidInterface;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function getById(TransactionId $id, UuidInterface $userId): Transaction;

    public function save(Transaction $transaction): void;

    /**
     * @throws NotFoundException
     */
    public function delete(TransactionId $id, UuidInterface $userId): void;
}

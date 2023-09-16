<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\SharedKernel\Exception\NotFoundException;
use Ramsey\Uuid\UuidInterface;

interface WalletRepository
{
    public function store(Wallet $wallet): void;

    public function existsByName(string $name, UuidInterface $userId): bool;

    /**
     * @throws NotFoundException
     */
    public function getById(WalletId $id, UuidInterface $userId): Wallet;

    public function save(Wallet $wallet): void;

    /**
     * @throws NotFoundException
     */
    public function delete(WalletId $id, UuidInterface $userId): void;
}

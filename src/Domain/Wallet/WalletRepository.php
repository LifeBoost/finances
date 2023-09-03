<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\SharedKernel\Exception\NotFoundException;

interface WalletRepository
{
    public function store(Wallet $wallet): void;

    public function existsByName(string $name): bool;

    /**
     * @throws NotFoundException
     */
    public function getById(WalletId $id): Wallet;

    public function save(Wallet $wallet): void;

    /**
     * @throws NotFoundException
     */
    public function delete(WalletId $id): void;
}

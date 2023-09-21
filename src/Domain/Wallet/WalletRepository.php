<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;

interface WalletRepository
{
    public function store(Wallet $wallet): void;

    public function existsByName(string $name): bool;

    /**
     * @throws NotFoundException
     */
    public function getById(Id $id): Wallet;

    public function save(Wallet $wallet): void;

    /**
     * @throws NotFoundException
     */
    public function delete(Id $id): void;
}

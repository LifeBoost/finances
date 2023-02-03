<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\User\UserId;

interface WalletRepository
{
    public function store(Wallet $wallet): void;
    public function exists(UserId $userId, string $name): bool;
}
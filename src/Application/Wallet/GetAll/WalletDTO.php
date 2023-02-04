<?php

declare(strict_types=1);

namespace App\Application\Wallet\GetAll;

final class WalletDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $startBalance,
        public readonly string $currency,
    ){}
}

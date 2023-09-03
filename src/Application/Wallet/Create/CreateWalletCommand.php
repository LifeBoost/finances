<?php

declare(strict_types=1);

namespace App\Application\Wallet\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateWalletCommand
{
    public function __construct(
        public readonly string $name,
        public readonly int $startBalance,
        public readonly string $currency,
    ){}
}

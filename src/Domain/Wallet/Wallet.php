<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Currency\Currency;

final class Wallet
{
    public function __construct(
        private WalletId $id,
        private string $name,
        private int $startBalance,
        private Currency $currency,
    ) {
    }

    public static function create(
        string $name,
        int $startBalance,
        Currency $currency,
    ): self {
        return new self(
            WalletId::generate(),
            $name,
            $startBalance,
            $currency,
        );
    }

    public function update(string $name, int $startBalance, Currency $currency): void
    {
        $this->name = $name;
        $this->startBalance = $startBalance;
        $this->currency = $currency;
    }

    public function getId(): WalletId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartBalance(): int
    {
        return $this->startBalance;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}

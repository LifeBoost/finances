<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Currency\Currency;
use App\Domain\User\UserId;

final class Wallet
{
    public function __construct(
        private WalletId $id,
        private UserId $userId,
        private string $name,
        private int $startBalance,
        private Currency $currency,
    ){}

    public static function create(
        UserId $userId,
        string $name,
        int $startBalance,
        Currency $currency,
    ): self {
        return new self(
            WalletId::generate(),
            $userId,
            $name,
            $startBalance,
            $currency,
        );
    }

    public function getId(): WalletId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
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

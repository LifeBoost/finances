<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Category\CategoryId;
use App\Domain\Wallet\WalletId;
use DateTimeImmutable;

final class Transaction
{
    public function __construct(
        private TransactionId $id,
        private TransactionType $type,
        private WalletId $sourceWallet,
        private ?WalletId $targetWallet,
        private CategoryId $categoryId,
        private DateTimeImmutable $date,
        private string $description,
        private int $amount,
    ){}

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getSourceWallet(): WalletId
    {
        return $this->sourceWallet;
    }

    public function getTargetWallet(): ?WalletId
    {
        return $this->targetWallet;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Category\CategoryId;
use App\Domain\Wallet\WalletId;
use App\SharedKernel\Exception\DomainException;
use DateTimeImmutable;

final class Transaction
{
    /**
     * @throws DomainException
     */
    public function __construct(
        private TransactionId $id,
        private TransactionType $type,
        private WalletId $sourceWalletId,
        private ?WalletId $targetWalletId,
        private ?CategoryId $categoryId,
        private DateTimeImmutable $date,
        private string $description,
        private int $amount,
    ) {
        $this->validateType($this->type, $this->targetWalletId, $this->categoryId);
    }

    /**
     * @throws DomainException
     */
    public static function create(
        TransactionType $type,
        WalletId $sourceWalletId,
        ?WalletId $targetWalletId,
        ?CategoryId $categoryId,
        DateTimeImmutable $date,
        string $description,
        int $amount
    ): self {
        return new self(
            TransactionId::generate(),
            $type,
            $sourceWalletId,
            $targetWalletId,
            $categoryId,
            $date,
            $description,
            $amount,
        );
    }

    /**
     * @throws DomainException
     */
    public function update(
        TransactionType $type,
        WalletId $sourceWalletId,
        ?WalletId $targetWalletId,
        ?CategoryId $categoryId,
        DateTimeImmutable $date,
        string $description,
        int $amount
    ): void {
        $this->validateType($type, $targetWalletId, $categoryId);

        $this->type = $type;
        $this->sourceWalletId = $sourceWalletId;
        $this->targetWalletId = $targetWalletId;
        $this->categoryId = $categoryId;
        $this->date = $date;
        $this->description = $description;
        $this->amount = $amount;
    }

    /**
     * @throws DomainException
     */
    private function validateType(TransactionType $type, ?WalletId $targetWalletId, ?CategoryId $categoryId): void
    {
        if ($type === TransactionType::TRANSFER && $targetWalletId === null) {
            throw new DomainException('For transfer type target wallet must be provided');
        }

        if ($type !== TransactionType::TRANSFER && $categoryId === null) {
            throw new DomainException(sprintf('For %s type category must be provided', $type->value));
        }
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getSourceWalletId(): WalletId
    {
        return $this->sourceWalletId;
    }

    public function getTargetWalletId(): ?WalletId
    {
        return $this->targetWalletId;
    }

    public function getCategoryId(): ?CategoryId
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

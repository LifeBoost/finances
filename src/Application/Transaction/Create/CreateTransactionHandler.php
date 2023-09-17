<?php

declare(strict_types=1);

namespace App\Application\Transaction\Create;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class CreateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $repository,
        private readonly WalletRepository $walletRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(CreateTransactionCommand $command): UuidInterface
    {
        $transaction = Transaction::create(
            TransactionType::from($command->type),
            $this->walletRepository->getById(WalletId::fromString($command->sourceWalletId)),
            $command->targetWalletId ? $this->walletRepository->getById(WalletId::fromString($command->targetWalletId)) : null,
            $command->categoryId ? $this->categoryRepository->getById(CategoryId::fromString($command->categoryId)) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
        );

        $this->repository->store($transaction);

        return $transaction->getId();
    }
}

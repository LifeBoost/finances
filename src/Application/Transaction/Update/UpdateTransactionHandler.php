<?php

declare(strict_types=1);

namespace App\Application\Transaction\Update;

use App\Domain\Category\CategoryRepository;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;

final readonly class UpdateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionRepository $repository,
        private WalletRepository $walletRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(UpdateTransactionCommand $command): void
    {
        $transaction = $this->repository->getById(Id::fromString($command->id));

        $transaction->update(
            TransactionType::from($command->type),
            $this->walletRepository->getById(Id::fromString($command->sourceWalletId)),
            $command->targetWalletId ? $this->walletRepository->getById(Id::fromString($command->targetWalletId)) : null,
            $command->categoryId ? $this->categoryRepository->getById(Id::fromString($command->categoryId)) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
        );

        $this->repository->save($transaction);
    }
}

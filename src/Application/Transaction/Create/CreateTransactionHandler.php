<?php

declare(strict_types=1);

namespace App\Application\Transaction\Create;

use App\Domain\Category\CategoryRepository;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\User\UserContext;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;

final readonly class CreateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(
        private TransactionRepository $repository,
        private WalletRepository $walletRepository,
        private CategoryRepository $categoryRepository,
        private UserContext $userContext,
    ) {}

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function __invoke(CreateTransactionCommand $command): Id
    {
        $transaction = Transaction::create(
            TransactionType::from($command->type),
            $this->walletRepository->getById(Id::fromString($command->sourceWalletId)),
            $command->targetWalletId ? $this->walletRepository->getById(Id::fromString($command->targetWalletId)) : null,
            $command->categoryId ? $this->categoryRepository->getById(Id::fromString($command->categoryId)) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
            $this->userContext,
        );

        $this->repository->store($transaction);

        return $transaction->getId();
    }
}

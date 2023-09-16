<?php

declare(strict_types=1);

namespace App\Application\Transaction\Update;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\User\UserContext;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final class UpdateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $repository,
        private readonly WalletRepository $walletRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly UserContext $userContext,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(UpdateTransactionCommand $command): void
    {
        $userId = Uuid::fromString($this->userContext->getUserId()->toString());
        $transaction = $this->repository->getById(TransactionId::fromString($command->id), $userId);

        $transaction->update(
            TransactionType::from($command->type),
            $this->walletRepository->getById(WalletId::fromString($command->sourceWalletId), $userId),
            $command->targetWalletId ? $this->walletRepository->getById(WalletId::fromString($command->targetWalletId), $userId) : null,
            $command->categoryId ? $this->categoryRepository->getById(CategoryId::fromString($command->categoryId), $userId) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
        );

        $this->repository->save($transaction);
    }
}

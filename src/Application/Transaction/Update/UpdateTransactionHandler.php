<?php

declare(strict_types=1);

namespace App\Application\Transaction\Update;

use App\Domain\Category\CategoryId;
use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\Wallet\WalletId;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;

final class UpdateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TransactionRepository $repository){}

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(UpdateTransactionCommand $command): void
    {
        $transaction = $this->repository->getById(TransactionId::fromString($command->id));

        $transaction->update(
            TransactionType::from($command->type),
            WalletId::fromString($command->sourceWalletId),
            $command->targetWalletId ? WalletId::fromString($command->targetWalletId) : null,
            $command->categoryId ? CategoryId::fromString($command->categoryId) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
        );

        $this->repository->save($transaction);
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Transaction\Create;

use App\Domain\Category\CategoryId;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\Wallet\WalletId;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use DateTimeImmutable;

final class CreateTransactionHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TransactionRepository $repository){}

    /**
     * @throws DomainException
     */
    public function __invoke(CreateTransactionCommand $command): TransactionId
    {
        $transaction = Transaction::create(
            TransactionType::from($command->type),
            WalletId::fromString($command->sourceWalletId),
            $command->targetWalletId ? WalletId::fromString($command->targetWalletId) : null,
            $command->categoryId ? CategoryId::fromString($command->categoryId) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $command->date),
            $command->description,
            $command->amount,
        );

        $this->repository->store($transaction);

        return $transaction->getId();
    }
}

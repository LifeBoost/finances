<?php

declare(strict_types=1);

namespace App\Infrastructure\Transaction;

use App\Domain\Category\CategoryId;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\User\UserContext;
use App\Domain\Wallet\WalletId;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class DoctrineTransactionRepository implements TransactionRepository
{
    public function __construct(
        private readonly UserContext $userContext,
        private readonly Connection $connection
    ) {
    }

    /**
     * @throws Exception
     */
    public function store(Transaction $transaction): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('transactions')
            ->values([
                'id' => ':id',
                'user_id' => ':userId',
                'type' => ':type',
                'source_wallet_id' => ':sourceWalletId',
                'target_wallet_id' => ':targetWalletId',
                'category_id' => ':categoryId',
                'date' => ':date',
                'description' => ':description',
                'amount' => ':amount',
            ])
            ->setParameters([
                'id' => $transaction->getId()->toString(),
                'userId' => $this->userContext->getUserId()->toString(),
                'type' => $transaction->getType()->value,
                'sourceWalletId' => $transaction->getSourceWalletId()->toString(),
                'targetWalletId' => $transaction->getTargetWalletId()?->toString(),
                'categoryId' => $transaction->getCategoryId()?->toString(),
                'date' => $transaction->getDate()->format('Y-m-d'),
                'description' => $transaction->getDescription(),
                'amount' => $transaction->getAmount(),
            ])
            ->executeStatement();
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     * @throws DomainException
     */
    public function getById(TransactionId $id): Transaction
    {
        $row = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'type',
                'source_wallet_id',
                'target_wallet_id',
                'category_id',
                'date',
                'description',
                'amount',
            )
            ->from('transactions')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row)) {
            throw new NotFoundException('Transaction with given ID not found');
        }

        return new Transaction(
            TransactionId::fromString($row['id']),
            TransactionType::from($row['type']),
            WalletId::fromString($row['source_wallet_id']),
            $row['target_wallet_id'] ? WalletId::fromString($row['target_wallet_id']) : null,
            $row['category_id'] ? CategoryId::fromString($row['category_id']) : null,
            DateTimeImmutable::createFromFormat('Y-m-d', $row['date']),
            $row['description'],
            (int) $row['amount'],
        );
    }

    /**
     * @throws Exception
     */
    public function save(Transaction $transaction): void
    {
        $this->connection
            ->createQueryBuilder()
            ->update('transactions')
            ->set('type', ':type')
            ->set('source_wallet_id', ':sourceWalletId')
            ->set('target_wallet_id', ':targetWalletId')
            ->set('category_id', ':categoryId')
            ->set('date', ':date')
            ->set('description', ':description')
            ->set('amount', ':amount')
            ->set('updated_at', ':updatedAt')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'type' => $transaction->getType()->value,
                'sourceWalletId' => $transaction->getSourceWalletId()->toString(),
                'targetWalletId' => $transaction->getTargetWalletId()?->toString(),
                'categoryId' => $transaction->getCategoryId()?->toString(),
                'date' => $transaction->getDate()->format('Y-m-d'),
                'description' => $transaction->getDescription(),
                'amount' => $transaction->getAmount(),
                'updatedAt' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $transaction->getId()->toString(),
            ])
            ->executeStatement();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function delete(TransactionId $id): void
    {
        $affectedRows = $this->connection
            ->createQueryBuilder()
            ->delete('transactions')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeStatement();

        if ($affectedRows === 0) {
            throw new NotFoundException('Transaction with given ID not found');
        }
    }
}

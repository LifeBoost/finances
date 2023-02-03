<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet;

use App\Domain\User\UserId;
use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class DoctrineWalletRepository implements WalletRepository
{
    public function __construct(private readonly Connection $connection){}

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function store(Wallet $wallet): void
    {
        try {
            $this->connection->beginTransaction();

            $this->connection
                ->createQueryBuilder()
                ->insert('wallets')
                ->values([
                    'id' => ':id',
                    'user_id' => ':userId',
                    'name' => ':name',
                    'start_balance' => ':startBalance',
                    'currency' => ':currency',
                ])
                ->setParameters([
                    'id' => $wallet->getId()->toString(),
                    'userId' => $wallet->getUserId()->toString(),
                    'name' => $wallet->getName(),
                    'startBalance' => $wallet->getStartBalance(),
                    'currency' => $wallet->getCurrency()->value,
                ])
                ->executeStatement();

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function exists(UserId $userId, string $name): bool
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('1')
            ->from('wallets')
            ->where('user_id = :userId')
            ->andWhere('name = :name')
            ->setParameters([
                'userId' => $userId->toString(),
                'name' => $name,
            ])
            ->executeQuery()
            ->rowCount() > 0;
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet;

use App\Domain\Currency\Currency;
use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Throwable;

final class DoctrineWalletRepository implements WalletRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserContext $userContext,
    ){}

    /**
     * @throws Throwable
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
                    'userId' => $this->userContext->getUserId()->toString(),
                    'name' => $wallet->getName(),
                    'startBalance' => $wallet->getStartBalance(),
                    'currency' => $wallet->getCurrency()->value,
                ])
                ->executeStatement();

            $this->connection->commit();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    /**
     * @throws Exception
     */
    public function existsByName(string $name): bool
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('1')
            ->from('wallets')
            ->where('user_id = :userId')
            ->andWhere('name = :name')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'name' => $name,
            ])
            ->executeQuery()
            ->rowCount() > 0;
    }

    /**
     * @throws Exception
     */
    public function getById(WalletId $id): Wallet
    {
        $row = $this->connection
            ->createQueryBuilder()
            ->select('id', 'name', 'start_balance', 'currency')
            ->from('wallets')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row)) {
            throw new NotFoundException('Wallet with given ID not found');
        }

        return new Wallet(
            WalletId::fromString($row['id']),
            $row['name'],
            (int) $row['start_balance'],
            Currency::from($row['currency']),
        );
    }

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function save(Wallet $wallet): void
    {
        try {
            $this->connection
                ->createQueryBuilder()
                ->update('wallets')
                ->set('name', ':name')
                ->set('start_balance', ':startBalance')
                ->set('currency', ':currency')
                ->set('updated_at', ':updatedAt')
                ->where('user_id = :userId')
                ->andWhere('id = :id')
                ->setParameters([
                    'name' => $wallet->getName(),
                    'startBalance' => $wallet->getStartBalance(),
                    'currency' => $wallet->getCurrency()->value,
                    'updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
                    'userId' => $this->userContext->getUserId()->toString(),
                    'id' => $wallet->getId()->toString(),
                ])
                ->executeStatement();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function delete(WalletId $id): void
    {
        $affectedRows = $this->connection
            ->createQueryBuilder()
            ->delete('wallets')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeStatement();

        if ($affectedRows === 0) {
            throw new NotFoundException('Wallet with given ID not found');
        }
    }
}

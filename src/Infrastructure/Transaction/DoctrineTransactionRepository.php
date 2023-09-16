<?php

declare(strict_types=1);

namespace App\Infrastructure\Transaction;

use App\Domain\Category\CategoryId;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\Transaction\TransactionType;
use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use App\Domain\Wallet\WalletId;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

final class DoctrineTransactionRepository extends ServiceEntityRepository implements TransactionRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @throws Exception
     */
    public function store(Transaction $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     * @throws DomainException
     */
    public function getById(TransactionId $id, UuidInterface $userId): Transaction
    {
        return $this->findOneBy([
            'id' => $id->toString(),
            'userId' => $userId->toString(),
        ]) ?? throw new NotFoundException('Transaction with given ID not found');
    }

    /**
     * @throws Exception
     */
    public function save(Transaction $transaction): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function delete(TransactionId $id, UuidInterface $userId): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id, $userId)
        );

        $this->getEntityManager()->flush();
    }
}

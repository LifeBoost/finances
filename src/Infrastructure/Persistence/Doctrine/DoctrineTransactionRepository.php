<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineTransactionRepository extends ServiceEntityRepository implements TransactionRepository
{
    public function __construct(ManagerRegistry $registry, private readonly UserContext $userContext)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function store(Transaction $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    public function getById(Id $id): Transaction
    {
        return $this->findOneBy([
            'id' => $id->toString(),
            'userId' => $this->userContext->getUserId(),
        ]) ?? throw new NotFoundException('Transaction with given ID not found');
    }

    public function save(Transaction $transaction): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(Id $id): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id)
        );

        $this->getEntityManager()->flush();
    }
}

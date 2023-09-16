<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet;

use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;
use Throwable;

final class DoctrineWalletRepository extends ServiceEntityRepository implements WalletRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function store(Wallet $wallet): void
    {
        $this->getEntityManager()->persist($wallet);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    public function existsByName(string $name, UuidInterface $userId): bool
    {
        return $this->findOneBy(['name' => $name, 'userId' => $userId->toString()]) !== null;
    }

    /**
     * @throws Exception
     */
    public function getById(WalletId $id, UuidInterface $userId): Wallet
    {
        return $this->findOneBy(['id' => $id->toString(), 'userId' => $userId->toString()]) ?? throw new NotFoundException('Wallet with given ID not found');
    }

    /**
     * @throws Throwable
     * @throws Exception
     */
    public function save(Wallet $wallet): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    public function delete(WalletId $id, UuidInterface $userId): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id, $userId)
        );
    }
}

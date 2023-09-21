<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\UserContext;
use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

final class DoctrineWalletRepository extends ServiceEntityRepository implements WalletRepository
{
    public function __construct(ManagerRegistry $registry, private readonly UserContext $userContext)
    {
        parent::__construct($registry, Wallet::class);
    }

    /**
     * @throws Throwable
     */
    public function store(Wallet $wallet): void
    {
        $this->getEntityManager()->persist($wallet);
        $this->getEntityManager()->flush();
    }

    public function existsByName(string $name): bool
    {
        return $this->findOneBy([
            'name' => $name,
            'userId' => $this->userContext->getUserId()->toString(),
        ]) !== null;
    }

    public function getById(Id $id): Wallet
    {
        return $this->findOneBy([
            'id' => $id->toString(),
            'userId' => $this->userContext->getUserId()->toString(),
        ]) ?? throw new NotFoundException('Wallet with given ID not found');
    }

    public function save(Wallet $wallet): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(Id $id): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id)
        );
    }
}

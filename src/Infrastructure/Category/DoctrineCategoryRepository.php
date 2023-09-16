<?php

declare(strict_types=1);

namespace App\Infrastructure\Category;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\SharedKernel\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

final class DoctrineCategoryRepository extends ServiceEntityRepository implements CategoryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws Exception
     */
    public function store(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    public function existsByName(string $name, CategoryType $type, UuidInterface $userId): bool
    {
        return $this->findOneBy([
            'name' => $name,
            'type' => $type->value,
            'userId' => $userId->toString(),
        ]) !== null;
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function getById(CategoryId $id, UuidInterface $userId): Category
    {
        return $this->findOneBy([
            'id' => $id->toString(),
            'userId' => $userId->toString(),
        ]) ?? throw new NotFoundException('Category with given ID not found');
    }

    /**
     * @throws Exception
     */
    public function save(Category $category): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function delete(CategoryId $id, UuidInterface $userId): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id, $userId)
        );
        $this->getEntityManager()->flush();
    }
}

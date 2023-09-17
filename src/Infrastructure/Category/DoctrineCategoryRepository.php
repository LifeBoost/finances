<?php

declare(strict_types=1);

namespace App\Infrastructure\Category;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

final class DoctrineCategoryRepository extends ServiceEntityRepository implements CategoryRepository
{
    public function __construct(ManagerRegistry $registry, private readonly UserContext $userContext)
    {
        parent::__construct($registry, Category::class);
    }

    public function store(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }

    public function existsByName(string $name, CategoryType $type): bool
    {
        return $this->findOneBy([
            'name' => $name,
            'type' => $type->value,
            'userId' => $this->userContext->getUserId()->toString(),
        ]) !== null;
    }

    public function getById(CategoryId $id): Category
    {
        return $this->findOneBy([
            'id' => $id->toString(),
            'userId' => $this->userContext->getUserId()->toString(),
        ]) ?? throw new NotFoundException('Category with given ID not found');
    }

    public function save(Category $category): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(CategoryId $id): void
    {
        $this->getEntityManager()->remove(
            $this->getById($id)
        );
        $this->getEntityManager()->flush();
    }
}

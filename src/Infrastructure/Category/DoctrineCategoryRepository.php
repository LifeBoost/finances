<?php

declare(strict_types=1);

namespace App\Infrastructure\Category;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class DoctrineCategoryRepository implements CategoryRepository
{
    public function __construct(private readonly Connection $connection, private readonly UserContext $userContext)
    {
    }

    /**
     * @throws Exception
     */
    public function store(Category $category): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('categories')
            ->values([
                'id' => ':id',
                'user_id' => ':userId',
                'type' => ':type',
                'name' => ':name',
                'icon' => ':icon'
            ])
            ->setParameters([
                'id' => $category->getId()->toString(),
                'userId' => $this->userContext->getUserId()->toString(),
                'type' => $category->getType()->value,
                'name' => $category->getName(),
                'icon' => $category->getIcon(),
            ])
            ->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function existsByName(string $name, CategoryType $type): bool
    {
        return $this->connection
                ->createQueryBuilder()
                ->select('1')
                ->from('categories')
                ->where('user_id = :userId')
                ->andWhere('name = :name')
                ->andWhere('type = :type')
                ->setParameters([
                    'userId' => $this->userContext->getUserId()->toString(),
                    'name' => $name,
                    'type' => $type->value,
                ])
                ->executeQuery()
                ->rowCount() > 0;
    }

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function getById(CategoryId $id): Category
    {
        $row = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'type',
                'name',
                'icon'
            )
            ->from('categories')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row)) {
            throw new NotFoundException('Category with given ID not found');
        }

        return new Category(
            CategoryId::fromString($row['id']),
            CategoryType::from($row['type']),
            $row['name'],
            $row['icon'],
        );
    }

    /**
     * @throws Exception
     */
    public function save(Category $category): void
    {
        $this->connection
            ->createQueryBuilder()
            ->update('categories')
            ->set('type', ':type')
            ->set('name', ':name')
            ->set('icon', ':icon')
            ->set('updated_at', ':updatedAt')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'type' => $category->getType()->value,
                'name' => $category->getName(),
                'icon' => $category->getIcon(),
                'updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $category->getId()->toString(),
            ])
            ->executeStatement();
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function delete(CategoryId $id): void
    {
        $affectedRows = $this->connection
            ->createQueryBuilder()
            ->delete('categories')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $id->toString(),
            ])
            ->executeStatement();

        if ($affectedRows === 0) {
            throw new NotFoundException('Category with given ID not found');
        }
    }
}

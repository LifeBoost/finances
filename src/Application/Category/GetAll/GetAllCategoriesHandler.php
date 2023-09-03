<?php

declare(strict_types=1);

namespace App\Application\Category\GetAll;

use App\Domain\User\UserContext;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetAllCategoriesHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserContext $userContext,
        private readonly Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(GetAllCategoriesQuery $query): CategoriesCollection
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'type',
                'name',
                'icon'
            )
            ->from('categories')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
            ]);

        if ($query->filterType) {
            $queryBuilder->andWhere('type = :type')->setParameter('type', $query->filterType);
        }

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        return new CategoriesCollection(
            ...array_map(
                static fn (array $row) => new CategoryDTO(
                    $row['id'],
                    $row['type'],
                    $row['name'],
                    $row['icon'],
                ),
                $rows
            )
        );
    }
}

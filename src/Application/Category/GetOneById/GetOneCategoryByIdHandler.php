<?php

declare(strict_types=1);

namespace App\Application\Category\GetOneById;

use App\Domain\User\UserContext;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetOneCategoryByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserContext $userContext,
    ){}

    /**
     * @throws Exception
     */
    public function __invoke(GetOneCategoryByIdQuery $query): CategoryDTO
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
                'id' => $query->id,
            ])
            ->executeQuery()
            ->fetchAssociative();

        return new CategoryDTO(
            $row['id'],
            $row['type'],
            $row['name'],
            $row['icon'],
        );
    }
}

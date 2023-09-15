<?php

declare(strict_types=1);

namespace App\Application\Category\GetOneById;

use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetOneCategoryByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private Connection  $connection,
        private UserContext $userContext,
    ) {
    }

    /**
     * @throws Exception
     * @throws NotFoundException
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

        if (empty($row)) {
            throw new NotFoundException('Category with given ID not found');
        }

        return new CategoryDTO(
            $row['id'],
            $row['type'],
            $row['name'],
            $row['icon'],
        );
    }
}

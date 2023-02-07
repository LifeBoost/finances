<?php

declare(strict_types=1);

namespace App\Application\Transaction\GetOneById;

use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetOneTransactionByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserContext $userContext,
        private readonly Connection $connection,
    ){}

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function __invoke(GetOneTransactionByIdQuery $query): TransactionDTO
    {
        $row = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'type',
                'source_wallet_id',
                'target_wallet_id',
                'category_id',
                'date',
                'description',
                'amount',
            )
            ->from('transactions')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $query->id,
            ])
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row)) {
            throw new NotFoundException('Transaction with given ID not found');
        }

        return new TransactionDTO(
            $row['id'],
            $row['type'],
            $row['source_wallet_id'],
            $row['target_wallet_id'],
            $row['category_id'],
            $row['date'],
            $row['description'],
            (int) $row['amount'],
        );
    }
}

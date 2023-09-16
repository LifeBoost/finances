<?php

declare(strict_types=1);

namespace App\Application\Transaction\GetAll;

use App\Domain\User\UserContext;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAllTransactionsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserContext $userContext,
        private readonly Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(GetAllTransactionsQuery $query): TransactionsCollection
    {
        $queryBuilder = $this->connection
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
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
            ]);

        if ($query->dateFrom && $query->dateTo) {
            $queryBuilder
                ->andWhere('date BETWEEN :dateFrom and :dateTo')
                ->setParameter('dateFrom', DateTimeImmutable::createFromFormat('Y-m-d', $query->dateFrom)->format('Y-m-d 00:00:00'))
                ->setParameter('dateTo', DateTimeImmutable::createFromFormat('Y-m-d', $query->dateTo)->format('Y-m-d 00:00:00'));
        }

        $rows = $queryBuilder->executeQuery()->fetchAllAssociative();

        return new TransactionsCollection(
            ...array_map(
                static fn (array $row) => new TransactionDTO(
                    $row['id'],
                    $row['type'],
                    $row['source_wallet_id'],
                    $row['target_wallet_id'],
                    $row['category_id'],
                    $row['date'],
                    $row['description'],
                    (int) $row['amount'],
                ),
                $rows
            )
        );
    }
}

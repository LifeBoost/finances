<?php

declare(strict_types=1);

namespace App\Application\Wallet\GetAll;

use App\Domain\User\UserContext;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetAllWalletsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserContext $userContext,
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(GetAllWalletsQuery $query): WalletsCollection
    {
        $rows = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'name',
                'start_balance',
                'currency',
            )
            ->from('wallets')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
            ])
            ->executeQuery()
            ->fetchAllAssociative();

        return new WalletsCollection(
            ...array_map(
                static fn (array $row) => new WalletDTO(
                    $row['id'],
                    $row['name'],
                    (int) $row['start_balance'],
                    $row['currency'],
                ),
                $rows
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Wallet\GetOneById;

use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\QueryHandlerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class GetOneWalletByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly UserContext $userContext,
    ){}

    /**
     * @throws Exception
     * @throws NotFoundException
     */
    public function __invoke(GetOneWalletByIdQuery $query): WalletDTO
    {
        $row = $this->connection
            ->createQueryBuilder()
            ->select(
                'id',
                'name',
                'start_balance',
                'currency'
            )
            ->from('wallets')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $this->userContext->getUserId()->toString(),
                'id' => $query->id,
            ])
            ->executeQuery()
            ->fetchAssociative();

        if (empty($row)) {
            throw new NotFoundException('Wallet with given ID not found');
        }

        return new WalletDTO(
            $row['id'],
            $row['name'],
            (int) $row['start_balance'],
            $row['currency'],
        );
    }
}

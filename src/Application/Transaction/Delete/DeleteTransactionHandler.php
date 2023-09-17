<?php

declare(strict_types=1);

namespace App\Application\Transaction\Delete;

use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

final readonly class DeleteTransactionHandler implements CommandHandlerInterface
{
    public function __construct(private TransactionRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(DeleteTransactionCommand $command): void
    {
        $this->repository->delete(TransactionId::fromString($command->id));
    }
}

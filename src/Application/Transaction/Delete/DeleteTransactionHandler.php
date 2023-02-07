<?php

declare(strict_types=1);

namespace App\Application\Transaction\Delete;

use App\Domain\Transaction\TransactionId;
use App\Domain\Transaction\TransactionRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final class DeleteTransactionHandler implements CommandHandlerInterface
{
    public function __construct(private readonly TransactionRepository $repository){}

    /**
     * @throws NotFoundException
     */
    public function __invoke(DeleteTransactionCommand $command): void
    {
        $this->repository->delete(TransactionId::fromString($command->id));
    }
}

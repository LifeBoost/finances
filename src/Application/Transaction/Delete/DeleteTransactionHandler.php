<?php

declare(strict_types=1);

namespace App\Application\Transaction\Delete;

use App\Domain\Transaction\TransactionRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;

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
        $this->repository->delete(Id::fromString($command->id));
    }
}

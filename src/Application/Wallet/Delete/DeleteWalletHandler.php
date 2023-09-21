<?php

declare(strict_types=1);

namespace App\Application\Wallet\Delete;

use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final readonly class DeleteWalletHandler implements CommandHandlerInterface
{
    public function __construct(private WalletRepository $repository) {}

    /**
     * @throws NotFoundException
     */
    public function __invoke(DeleteWalletCommand $command): void
    {
        $this->repository->delete(Id::fromString($command->id));
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Wallet\Delete;

use App\Domain\User\UserContext;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

final class DeleteWalletHandler implements CommandHandlerInterface
{
    public function __construct(private readonly WalletRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(DeleteWalletCommand $command): void
    {
        $this->repository->delete(WalletId::fromString($command->id));
    }
}

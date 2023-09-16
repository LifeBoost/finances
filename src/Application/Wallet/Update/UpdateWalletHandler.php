<?php

declare(strict_types=1);

namespace App\Application\Wallet\Update;

use App\Domain\Currency\Currency;
use App\Domain\User\UserContext;
use App\Domain\Wallet\WalletId;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

final class UpdateWalletHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly WalletRepository $walletRepository,
        private readonly UserContext $userContext,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(UpdateWalletCommand $command): void
    {
        $wallet = $this->walletRepository->getById(WalletId::fromString($command->id), Uuid::fromString($this->userContext->getUserId()->toString()));

        $wallet->update(
            $command->name,
            $command->startBalance,
            Currency::from($command->currency),
        );

        $this->walletRepository->save($wallet);
    }
}

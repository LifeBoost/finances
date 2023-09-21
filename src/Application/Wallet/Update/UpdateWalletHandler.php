<?php

declare(strict_types=1);

namespace App\Application\Wallet\Update;

use App\Domain\Currency\Currency;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final readonly class UpdateWalletHandler implements CommandHandlerInterface
{
    public function __construct(
        private WalletRepository $walletRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(UpdateWalletCommand $command): void
    {
        $wallet = $this->walletRepository->getById(Id::fromString($command->id));

        $wallet->update(
            $command->name,
            $command->startBalance,
            Currency::from($command->currency),
        );

        $this->walletRepository->save($wallet);
    }
}

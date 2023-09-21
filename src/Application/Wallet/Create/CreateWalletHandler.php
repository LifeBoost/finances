<?php

declare(strict_types=1);

namespace App\Application\Wallet\Create;

use App\Domain\Currency\Currency;
use App\Domain\User\UserContext;
use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final readonly class CreateWalletHandler implements CommandHandlerInterface
{
    public function __construct(
        private WalletRepository $repository,
        private UserContext $userContext,
    ) {
    }

    /**
     * @throws DomainException
     */
    public function __invoke(CreateWalletCommand $command): Id
    {
        if ($this->repository->existsByName($command->name)) {
            throw new DomainException('Wallet with given name already exists');
        }

        $wallet = Wallet::create(
            $command->name,
            $command->startBalance,
            Currency::from($command->currency),
            $this->userContext,
        );

        $this->repository->store($wallet);

        return $wallet->getId();
    }
}

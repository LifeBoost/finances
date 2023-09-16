<?php

declare(strict_types=1);

namespace App\Application\Wallet\Create;

use App\Domain\Currency\Currency;
use App\Domain\User\UserContext;
use App\Domain\Wallet\Wallet;
use App\Domain\Wallet\WalletRepository;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CreateWalletHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly WalletRepository $repository,
        private readonly UserContext $userContext,
    ) {
    }

    public function __invoke(CreateWalletCommand $command): UuidInterface
    {
        if ($this->repository->existsByName($command->name, Uuid::fromString($this->userContext->getUserId()->toString()))) {
            throw new DomainException('Wallet with given name already exists');
        }

        $wallet = Wallet::create(
            Uuid::fromString($this->userContext->getUserId()->toString()),
            $command->name,
            $command->startBalance,
            Currency::from($command->currency),
        );

        $this->repository->store($wallet);

        return $wallet->getId();
    }
}

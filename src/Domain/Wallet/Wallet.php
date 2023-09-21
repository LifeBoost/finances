<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Currency\Currency;
use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use App\SharedKernel\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table(name: 'wallets')]
#[ORM\UniqueConstraint(name: "wallets_name_unique_index", columns: ['name', 'user_id'])]
class Wallet
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id $id,
        #[ORM\Column(type: 'uuid_user')]
        private readonly UserId $userId,
        #[ORM\Column]
        private string $name,
        #[ORM\Column]
        private int $startBalance,
        #[ORM\Column]
        private string $currency,
    ) {
    }

    public static function create(
        string $name,
        int $startBalance,
        Currency $currency,
        UserContext $userContext,
    ): self {
        return new self(
            Id::generate(),
            $userContext->getUserId(),
            $name,
            $startBalance,
            $currency->value,
        );
    }

    public function update(string $name, int $startBalance, Currency $currency): void
    {
        $this->name = $name;
        $this->startBalance = $startBalance;
        $this->currency = $currency->value;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartBalance(): int
    {
        return $this->startBalance;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}

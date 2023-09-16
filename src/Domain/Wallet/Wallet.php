<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Currency\Currency;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table('wallets')]
class Wallet
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private UuidInterface $id,
        #[ORM\Column(type: 'uuid')]
        public readonly UuidInterface $userId,
        #[ORM\Column]
        private string $name,
        #[ORM\Column]
        private int $startBalance,
        #[ORM\Column]
        private string $currency,
    ) {
    }

    public static function create(
        UuidInterface $userId,
        string $name,
        int $startBalance,
        Currency $currency,
    ): self {
        return new self(
            Uuid::uuid4(),
            $userId,
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

    public function getId(): UuidInterface
    {
        return $this->id;
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

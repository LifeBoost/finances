<?php

declare(strict_types=1);

namespace App\Domain\Wallet;

use App\Domain\Currency\Currency;
use App\SharedKernel\Entity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table('wallets')]
class Wallet extends Entity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private UuidInterface $id,
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
    ): self {
        return new self(
            Uuid::uuid4(),
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

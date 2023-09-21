<?php

declare(strict_types=1);

namespace App\UI\API\Request\Wallet;

use App\Domain\Currency\Currency;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateWalletRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(0)]
        public int $startBalance,
        #[Assert\NotBlank]
        #[Assert\Choice([
            Currency::PLN->value,
            Currency::EUR->value,
            Currency::USD->value,
        ])]
        public string $currency,
    ) {}
}

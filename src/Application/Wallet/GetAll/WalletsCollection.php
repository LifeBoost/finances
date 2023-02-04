<?php

declare(strict_types=1);

namespace App\Application\Wallet\GetAll;

final class WalletsCollection
{
    private readonly array $items;

    public function __construct(WalletDTO ...$items)
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
        return $this->items;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Wallet\Delete;

final class DeleteWalletCommand
{
    public function __construct(public readonly string $id)
    {
    }
}

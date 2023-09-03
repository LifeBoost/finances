<?php

declare(strict_types=1);

namespace App\Application\Transaction\Delete;

final class DeleteTransactionCommand
{
    public function __construct(public readonly string $id)
    {
    }
}

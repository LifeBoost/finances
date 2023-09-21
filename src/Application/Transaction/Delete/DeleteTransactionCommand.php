<?php

declare(strict_types=1);

namespace App\Application\Transaction\Delete;

final readonly class DeleteTransactionCommand
{
    public function __construct(public string $id) {}
}

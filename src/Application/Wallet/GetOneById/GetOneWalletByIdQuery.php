<?php

declare(strict_types=1);

namespace App\Application\Wallet\GetOneById;

final class GetOneWalletByIdQuery
{
    public function __construct(public readonly string $id) {}
}

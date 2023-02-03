<?php

declare(strict_types=1);

namespace App\Domain\Currency;

enum Currency: string
{
    case PLN = 'PLN';
    case USD = 'USD';
    case EUR = 'EUR';
}

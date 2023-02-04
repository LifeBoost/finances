<?php

declare(strict_types=1);

namespace App\Domain\Category;

enum CategoryType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
}

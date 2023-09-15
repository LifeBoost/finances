<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Mother;

use App\Domain\Category\CategoryType;

final readonly class CategoryMother extends AbstractMother
{
    public static function prepareJsonData(string $type = CategoryType::INCOME->value, string $name = 'Name Income', string $icon = 'dashboard'): array
    {
        return [
            'type' => $type,
            'name' => $name,
            'icon' => $icon,
        ];
    }

    public static function getUrlPattern(): string
    {
        return 'api/v1/categories';
    }
}

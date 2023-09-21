<?php

declare(strict_types=1);

namespace App\UI\API\Request\Category;

use App\Domain\Category\CategoryType;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: [
            CategoryType::EXPENSE->value,
            CategoryType::INCOME->value,
        ])]
        public string $type,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public string $name,
        #[Assert\NotBlank]
        public string $icon,
    ) {}
}

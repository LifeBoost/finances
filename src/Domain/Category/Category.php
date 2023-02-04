<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class Category
{
    public function __construct(
        private CategoryId $id,
        private CategoryType $type,
        private string $name,
        private string $icon,
    ){}

    public static function create(
        CategoryType $type,
        string $name,
        string $icon,
    ): self {
        return new self(
            CategoryId::generate(),
            $type,
            $name,
            $icon,
        );
    }

    public function update(
        CategoryType $type,
        string $name,
        string $icon,
    ): void {
        $this->type = $type;
        $this->name = $name;
        $this->icon = $icon;
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getType(): CategoryType
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }
}

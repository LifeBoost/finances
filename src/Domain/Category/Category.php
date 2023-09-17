<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\SharedKernel\Entity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
class Category extends Entity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private UuidInterface $id,
        #[ORM\Column()]
        private string $type,
        #[ORM\Column()]
        private string $name,
        #[ORM\Column()]
        private string $icon,
    ) {
    }

    public static function create(
        CategoryType $type,
        string $name,
        string $icon,
    ): self {
        return new self(
            Uuid::uuid4(),
            $type->value,
            $name,
            $icon,
        );
    }

    public function update(
        CategoryType $type,
        string $name,
        string $icon,
    ): void {
        $this->type = $type->value;
        $this->name = $name;
        $this->icon = $icon;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getType(): string
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

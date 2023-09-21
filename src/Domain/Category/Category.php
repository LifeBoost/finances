<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use App\SharedKernel\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\UniqueConstraint(name: 'categories_name_type_unique_index', columns: ['type', 'name', 'user_id'])]
class Category
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id $id,
        #[ORM\Column(type: 'uuid_user')]
        private readonly UserId $userId,
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
        UserContext $userContext,
    ): self {
        return new self(
            Id::generate(),
            $userContext->getUserId(),
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

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
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

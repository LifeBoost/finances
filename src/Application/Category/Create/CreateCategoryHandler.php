<?php

declare(strict_types=1);

namespace App\Application\Category\Create;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class CreateCategoryHandler implements CommandHandlerInterface
{
    public function __construct(
        private CategoryRepository $repository,
    ) {
    }

    public function __invoke(CreateCategoryCommand $command): UuidInterface
    {
        if ($this->repository->existsByName($command->name, CategoryType::from($command->type))) {
            throw new DomainException('Category with given name and type already exists');
        }

        $category = Category::create(
            CategoryType::from($command->type),
            $command->name,
            $command->icon,
        );

        $this->repository->store($category);

        return $category->getId();
    }
}

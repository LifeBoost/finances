<?php

declare(strict_types=1);

namespace App\Application\Category\Create;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\Domain\User\UserContext;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CreateCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CategoryRepository $repository, private readonly UserContext $userContext)
    {
    }

    public function __invoke(CreateCategoryCommand $command): UuidInterface
    {
        if ($this->repository->existsByName($command->name, CategoryType::from($command->type), Uuid::fromString($this->userContext->getUserId()->toString()))) {
            throw new DomainException('Category with given name and type already exists');
        }

        $category = Category::create(
            Uuid::fromString($this->userContext->getUserId()->toString()),
            CategoryType::from($command->type),
            $command->name,
            $command->icon,
        );

        $this->repository->store($category);

        return $category->getId();
    }
}

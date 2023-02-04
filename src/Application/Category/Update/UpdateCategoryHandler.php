<?php

declare(strict_types=1);

namespace App\Application\Category\Update;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final class UpdateCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CategoryRepository $repository){}

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->repository->getById(CategoryId::fromString($command->id));

        $category->update(
            CategoryType::from($command->type),
            $command->name,
            $command->icon,
        );

        if ($this->repository->existsByName($category->getName(), $category->getType())) {
            throw new DomainException('Category with given name and type already exists');
        }

        $this->repository->save($category);
    }
}

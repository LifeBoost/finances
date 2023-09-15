<?php

declare(strict_types=1);

namespace App\Application\Category\Update;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryType;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(UpdateCategoryCommand $command): void
    {
        $category = $this->repository->getById(CategoryId::fromString($command->id));

        if (
            $category->getName() === $command->name
            && $category->getType()->value === $command->type
            && $category->getIcon() !== $command->icon
        ) {
            $category->update(
                CategoryType::from($command->type),
                $command->name,
                $command->icon,
            );

            $this->repository->save($category);

            return;
        }

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

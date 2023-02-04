<?php

declare(strict_types=1);

namespace App\Application\Category\Delete;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final class DeleteCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CategoryRepository $repository){}

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $this->repository->delete(CategoryId::fromString($command->id));
    }
}

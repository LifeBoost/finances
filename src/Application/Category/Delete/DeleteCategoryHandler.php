<?php

declare(strict_types=1);

namespace App\Application\Category\Delete;

use App\Domain\Category\CategoryRepository;
use App\SharedKernel\Id;
use App\SharedKernel\Messenger\CommandHandlerInterface;

final readonly class DeleteCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private CategoryRepository $repository) {}

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $this->repository->delete(Id::fromString($command->id));
    }
}

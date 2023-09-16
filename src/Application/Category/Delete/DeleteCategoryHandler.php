<?php

declare(strict_types=1);

namespace App\Application\Category\Delete;

use App\Domain\Category\CategoryId;
use App\Domain\Category\CategoryRepository;
use App\Domain\User\UserContext;
use App\SharedKernel\Messenger\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

final class DeleteCategoryHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CategoryRepository $repository, private readonly UserContext $userContext)
    {
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        $this->repository->delete(CategoryId::fromString($command->id), Uuid::fromString($this->userContext->getUserId()->toString()));
    }
}

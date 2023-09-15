<?php

declare(strict_types=1);

namespace App\UI\API\Controller;

use App\Application\Category\Create\CreateCategoryCommand;
use App\Application\Category\Delete\DeleteCategoryCommand;
use App\Application\Category\GetAll\CategoriesCollection;
use App\Application\Category\GetAll\GetAllCategoriesQuery;
use App\Application\Category\GetOneById\CategoryDTO;
use App\Application\Category\GetOneById\GetOneCategoryByIdQuery;
use App\Application\Category\Update\UpdateCategoryCommand;
use App\Domain\Category\CategoryId;
use App\UI\API\Request\Category\CreateCategoryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/categories', name: 'api.v1.categories.')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateCategoryRequest $request): Response
    {
        /** @var CategoryId $id */
        $id = $this->bus
            ->dispatch(
                new CreateCategoryCommand(
                    $request->type,
                    $request->name,
                    $request->icon,
                )
            )
            ->last(HandledStamp::class)
            ?->getResult();

        return new JsonResponse([
            'id' => $id->toString(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $this->bus->dispatch(
            new DeleteCategoryCommand($id)
        );

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'update', methods: ['POST'])]
    public function update(string $id, #[MapRequestPayload] CreateCategoryRequest $request): Response
    {
        $this->bus->dispatch(
            new UpdateCategoryCommand(
                $id,
                $request->type,
                $request->name,
                $request->icon,
            )
        );

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function one(string $id): Response
    {
        $query = new GetOneCategoryByIdQuery($id);

        /** @var CategoryDTO $dto */
        $dto = $this->bus->dispatch($query)->last(HandledStamp::class)?->getResult();

        return new JsonResponse([
            'id' => $dto->id,
            'type' => $dto->type,
            'name' => $dto->name,
            'icon' => $dto->icon,
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(#[MapQueryString] GetAllCategoriesQuery $query = new GetAllCategoriesQuery()): Response
    {
        /** @var CategoriesCollection $collection */
        $collection = $this->bus->dispatch($query)->last(HandledStamp::class)?->getResult();

        return new JsonResponse(
            array_map(static fn (\App\Application\Category\GetAll\CategoryDTO $dto) => [
                'id' => $dto->id,
                'type' => $dto->type,
                'name' => $dto->name,
                'icon' => $dto->icon,
            ], $collection->toArray())
        );
    }
}

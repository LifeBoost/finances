<?php

declare(strict_types=1);

namespace App\UI\API\Category;

use App\Application\Category\GetAll\CategoriesCollection;
use App\Application\Category\GetAll\CategoryDTO;
use App\Application\Category\GetAll\GetAllCategoriesQuery;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetAllCategoriesAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $queryBus){}

    public function __invoke(Request $request): Response
    {
        $query = new GetAllCategoriesQuery($request->get('type'));

        /** @var CategoriesCollection $collection */
        $collection = $this->queryBus->dispatch($query)->last(HandledStamp::class)->getResult();

        return new JsonResponse(
            array_map(static fn (CategoryDTO $dto) => [
                'id' => $dto->id,
                'type' => $dto->type,
                'name' => $dto->name,
                'icon' => $dto->icon,
            ], $collection->toArray())
        );
    }
}

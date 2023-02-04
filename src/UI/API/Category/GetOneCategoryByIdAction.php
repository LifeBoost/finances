<?php

declare(strict_types=1);

namespace App\UI\API\Category;

use App\Application\Category\GetOneById\CategoryDTO;
use App\Application\Category\GetOneById\GetOneCategoryByIdQuery;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetOneCategoryByIdAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $queryBus){}

    public function __invoke(string $id): Response
    {
        $query = new GetOneCategoryByIdQuery($id);

        /** @var CategoryDTO $dto */
        $dto = $this->queryBus->dispatch($query)->last(HandledStamp::class)->getResult();

        return new JsonResponse([
            'id' => $dto->id,
            'type' => $dto->type,
            'name' => $dto->name,
            'icon' => $dto->icon,
        ]);
    }
}

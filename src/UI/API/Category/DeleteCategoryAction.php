<?php

declare(strict_types=1);

namespace App\UI\API\Category;

use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteCategoryAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $messageBus){}

    public function __invoke(string $id): Response
    {
        $command = CategoryCommandFactory::makeDeleteCommand($id);

        $this->messageBus->dispatch($command);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}

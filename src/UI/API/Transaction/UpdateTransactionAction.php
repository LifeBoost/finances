<?php

declare(strict_types=1);

namespace App\UI\API\Transaction;

use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateTransactionAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $commandBus){}

    public function __invoke(string $id, Request $request): Response
    {
        $command = TransactionCommandFactory::makeUpdateCommand($id, $request);

        $this->commandBus->dispatch($command);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}

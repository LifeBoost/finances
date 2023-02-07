<?php

declare(strict_types=1);

namespace App\UI\API\Transaction;

use App\Domain\Transaction\TransactionId;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateTransactionAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $commandBus){}

    public function __invoke(Request $request): Response
    {
        $command = TransactionCommandFactory::makeCreateCommand($request);

        /** @var TransactionId $id */
        $id = $this->commandBus->dispatch($command)->last(HandledStamp::class)?->getResult();

        return new JsonResponse(['id' => $id->toString()], Response::HTTP_CREATED);
    }
}

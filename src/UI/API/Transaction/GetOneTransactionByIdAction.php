<?php

declare(strict_types=1);

namespace App\UI\API\Transaction;

use App\Application\Transaction\GetOneById\GetOneTransactionByIdQuery;
use App\Application\Transaction\GetOneById\TransactionDTO;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetOneTransactionByIdAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $queryBus){}

    public function __invoke(string $id): Response
    {
        /** @var TransactionDTO $dto */
        $dto = $this->queryBus->dispatch(new GetOneTransactionByIdQuery($id))->last(HandledStamp::class)?->getResult();

        return new JsonResponse([
            'id' => $dto->id,
            'type' => $dto->type,
            'sourceWalletId' => $dto->sourceWalletId,
            'targetWalletId' => $dto->targetWalletId,
            'categoryId' => $dto->categoryId,
            'date' => $dto->date,
            'description' => $dto->description,
            'amount' => $dto->amount,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\UI\API\Transaction;

use App\Application\Transaction\GetAll\GetAllTransactionsQuery;
use App\Application\Transaction\GetAll\TransactionDTO;
use App\Application\Transaction\GetAll\TransactionsCollection;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetAllTransactionsAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $queryBus){}

    public function __invoke(Request $request): Response
    {
        $query = new GetAllTransactionsQuery($request->get('dateFrom'), $request->get('dateTo'));

        /** @var TransactionsCollection $collection */
        $collection = $this->queryBus->dispatch($query)->last(HandledStamp::class)?->getResult();

        return new JsonResponse(
            array_map(static fn (TransactionDTO $dto) => [
                'id' => $dto->id,
                'type' => $dto->type,
                'sourceWalletId' => $dto->sourceWalletId,
                'targetWalletId' => $dto->targetWalletId,
                'categoryId' => $dto->categoryId,
                'date' => $dto->date,
                'description' => $dto->description,
                'amount' => $dto->amount,
            ], $collection->toArray())
        );
    }
}

<?php

declare(strict_types=1);

namespace App\UI\API\Controller;

use App\Application\Transaction\Create\CreateTransactionCommand;
use App\Application\Transaction\Delete\DeleteTransactionCommand;
use App\Application\Transaction\GetAll\GetAllTransactionsQuery;
use App\Application\Transaction\GetAll\TransactionDTO;
use App\Application\Transaction\GetAll\TransactionsCollection;
use App\Application\Transaction\GetOneById\GetOneTransactionByIdQuery;
use App\Application\Transaction\Update\UpdateTransactionCommand;
use App\SharedKernel\Id;
use App\UI\API\Request\Transaction\CreateTransactionRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/transactions', name: 'api.v1.transactions.')]
final class TransactionController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateTransactionRequest $request): Response
    {
        /** @var Id $id */
        $id = $this->bus
            ->dispatch(
                new CreateTransactionCommand(
                    $request->type,
                    $request->sourceWalletId,
                    $request->targetWalletId,
                    $request->categoryId,
                    $request->date,
                    $request->description,
                    $request->amount,
                )
            )->last(HandledStamp::class)
            ?->getResult();

        return new JsonResponse(['id' => $id->toString()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $this->bus->dispatch(
            new DeleteTransactionCommand($id)
        );

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'update', methods: ['POST'])]
    public function update(string $id, #[MapRequestPayload] CreateTransactionRequest $request): Response
    {
        $this->bus->dispatch(
            new UpdateTransactionCommand(
                $id,
                $request->type,
                $request->sourceWalletId,
                $request->targetWalletId,
                $request->categoryId,
                $request->date,
                $request->description,
                $request->amount,
            )
        );

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(#[MapQueryString] GetAllTransactionsQuery $query = new GetAllTransactionsQuery(null, null)): Response
    {
        /** @var TransactionsCollection $collection */
        $collection = $this->bus->dispatch($query)->last(HandledStamp::class)?->getResult();

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

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function one(string $id): Response
    {
        /** @var \App\Application\Transaction\GetOneById\TransactionDTO $dto */
        $dto = $this->bus->dispatch(new GetOneTransactionByIdQuery($id))->last(HandledStamp::class)?->getResult();

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

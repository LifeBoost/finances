<?php

declare(strict_types=1);

namespace App\UI\API\Controller;

use App\Application\Wallet\Create\CreateWalletCommand;
use App\Application\Wallet\Delete\DeleteWalletCommand;
use App\Application\Wallet\GetAll\GetAllWalletsQuery;
use App\Application\Wallet\GetAll\WalletsCollection;
use App\Application\Wallet\GetOneById\GetOneWalletByIdQuery;
use App\Application\Wallet\GetOneById\WalletDTO;
use App\Application\Wallet\Update\UpdateWalletCommand;
use App\Domain\Wallet\WalletId;
use App\UI\API\Request\Wallet\CreateWalletRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/wallets', name: 'api.v1.wallets.')]
final class WalletController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly MessageBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateWalletRequest $request): Response
    {
        /** @var WalletId $id */
        $id = $this->commandBus
            ->dispatch(
                new CreateWalletCommand(
                    $request->name,
                    $request->startBalance,
                    $request->currency,
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
        $this->commandBus->dispatch(new DeleteWalletCommand($id));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'update', methods: ['POST'])]
    public function update(string $id, #[MapRequestPayload] CreateWalletRequest $request): Response
    {
        $this->commandBus->dispatch(
            new UpdateWalletCommand(
                $id,
                $request->name,
                $request->startBalance,
                $request->currency,
            )
        );

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'one', methods: ['GET'])]
    public function one(string $id): Response
    {
        /** @var WalletDTO $dto */
        $dto = $this->queryBus->dispatch(new GetOneWalletByIdQuery($id))->last(HandledStamp::class)->getResult();

        return new JsonResponse([
            'id' => $dto->id,
            'name' => $dto->name,
            'startBalance' => $dto->startBalance,
            'currency' => $dto->currency,
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        /** @var WalletsCollection $collection */
        $collection = $this->queryBus->dispatch(new GetAllWalletsQuery())->last(HandledStamp::class)->getResult();

        return new JsonResponse(
            array_map(static fn (\App\Application\Wallet\GetAll\WalletDTO $dto) => [
                'id' => $dto->id,
                'name' => $dto->name,
                'startBalance' => $dto->startBalance,
                'currency' => $dto->currency,
            ], $collection->toArray())
        );
    }
}

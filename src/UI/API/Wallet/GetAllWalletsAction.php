<?php

declare(strict_types=1);

namespace App\UI\API\Wallet;

use App\Application\Wallet\GetAll\GetAllWalletsQuery;
use App\Application\Wallet\GetAll\WalletDTO;
use App\Application\Wallet\GetAll\WalletsCollection;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetAllWalletsAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $queryBus){}

    public function __invoke(): Response
    {
        /** @var WalletsCollection $collection */
        $collection = $this->queryBus->dispatch(new GetAllWalletsQuery())->last(HandledStamp::class)->getResult();

        return new JsonResponse(
            array_map(static fn (WalletDTO $dto) => [
                'id' => $dto->id,
                'name' => $dto->name,
                'startBalance' => $dto->startBalance,
                'currency' => $dto->currency,
            ], $collection->toArray())
        );
    }
}

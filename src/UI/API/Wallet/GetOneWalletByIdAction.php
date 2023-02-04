<?php

declare(strict_types=1);

namespace App\UI\API\Wallet;

use App\Application\Wallet\GetOneById\GetOneWalletByIdQuery;
use App\Application\Wallet\GetOneById\WalletDTO;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetOneWalletByIdAction extends AbstractAction
{
    public function __construct(
        private readonly MessageBusInterface $queryBus
    ){}

    public function __invoke(string $id): Response
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
}

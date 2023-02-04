<?php

declare(strict_types=1);

namespace App\UI\API\Wallet;

use App\Domain\Wallet\WalletId;
use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateWalletAction extends AbstractAction
{
    public function __construct(private readonly MessageBusInterface $commandBus){}

    public function __invoke(Request $request): Response
    {
        $command = WalletCommandFactory::makeCreateCommand($request);

        /** @var WalletId $id */
        $id = $this->commandBus->dispatch($command)->last(HandledStamp::class)->getResult();

        return new JsonResponse([
            'id' => $id->toString(),
        ]);
    }
}

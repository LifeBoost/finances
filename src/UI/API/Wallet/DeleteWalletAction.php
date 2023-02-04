<?php

declare(strict_types=1);

namespace App\UI\API\Wallet;

use App\UI\API\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteWalletAction extends AbstractAction
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ){}

    public function __invoke(string $id): Response
    {
        $command = WalletCommandFactory::makeDeleteCommand($id);

        $this->commandBus->dispatch($command);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

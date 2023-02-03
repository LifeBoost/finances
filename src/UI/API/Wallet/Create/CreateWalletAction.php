<?php

declare(strict_types=1);

namespace App\UI\API\Wallet\Create;

use App\Application\Wallet\Create\CreateWalletCommand;
use App\Domain\Wallet\WalletId;
use App\UI\API\AbstractAction;
use Assert\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateWalletAction extends AbstractAction
{
    private const NAME = 'name';
    private const START_BALANCE = 'startBalance';
    private const CURRENCY = 'currency';

    public function __construct(private readonly MessageBusInterface $commandBus){}

    public function __invoke(Request $request): Response
    {
        $data = $request->toArray();

        Assert::lazy()
            ->that($data[self::NAME] ?? null, self::NAME)->notEmpty('Name is required')
            ->that($data[self::START_BALANCE] ?? null, self::START_BALANCE)->notNull('Start balance is required')->integer('Start balance can only be a type of integer')
            ->that($data[self::CURRENCY] ?? null, self::CURRENCY)->notEmpty('Currency is required')
            ->verifyNow();

        $command = new CreateWalletCommand(
            $data[self::NAME],
            (int) $data[self::START_BALANCE],
            $data[self::CURRENCY]
        );

        /** @var WalletId $id */
        $id = $this->commandBus->dispatch($command)->last(HandledStamp::class)->getResult();

        return new JsonResponse([
            'id' => $id->toString(),
        ]);
    }
}

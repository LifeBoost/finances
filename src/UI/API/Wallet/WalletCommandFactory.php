<?php

declare(strict_types=1);

namespace App\UI\API\Wallet;

use App\Application\Wallet\Create\CreateWalletCommand;
use App\Application\Wallet\Delete\DeleteWalletCommand;
use App\Application\Wallet\Update\UpdateWalletCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request;

final class WalletCommandFactory
{
    public const NAME = 'name';
    public const START_BALANCE = 'startBalance';
    public const CURRENCY = 'currency';

    public static function makeCreateCommand(Request $request): CreateWalletCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new CreateWalletCommand(
            $data[self::NAME],
            (int) $data[self::START_BALANCE],
            $data[self::CURRENCY],
        );
    }

    public static function makeUpdateCommand(string $id, Request $request): UpdateWalletCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new UpdateWalletCommand(
            $id,
            $data[self::NAME],
            (int) $data[self::START_BALANCE],
            $data[self::CURRENCY],
        );
    }

    public static function makeDeleteCommand(string $id): DeleteWalletCommand
    {
        return new DeleteWalletCommand($id);
    }

    private static function validateRequest(array $data): void
    {
        Assert::lazy()
            ->that($data[self::NAME] ?? null, self::NAME)->notEmpty('Name is required')
            ->that($data[self::START_BALANCE] ?? null, self::START_BALANCE)->notNull('Start balance is required')->integer('Start balance can only be a type of integer')
            ->that($data[self::CURRENCY] ?? null, self::CURRENCY)->notEmpty('Currency is required')
            ->verifyNow();
    }
}

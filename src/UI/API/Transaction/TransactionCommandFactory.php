<?php

declare(strict_types=1);

namespace App\UI\API\Transaction;

use App\Application\Transaction\Create\CreateTransactionCommand;
use App\Application\Transaction\Delete\DeleteTransactionCommand;
use App\Application\Transaction\Update\UpdateTransactionCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request;

final class TransactionCommandFactory
{
    private const TYPE = 'type';
    private const SOURCE_WALLET_ID = 'sourceWalletId';
    private const TARGET_WALLET_ID = 'targetWalletId';
    private const CATEGORY_ID = 'categoryId';
    private const DATE = 'date';
    private const DESCRIPTION = 'description';
    private const AMOUNT = 'amount';

    public static function makeCreateCommand(Request $request): CreateTransactionCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new CreateTransactionCommand(
            $data[self::TYPE],
            $data[self::SOURCE_WALLET_ID],
            $data[self::TARGET_WALLET_ID],
            $data[self::CATEGORY_ID],
            $data[self::DATE],
            $data[self::DESCRIPTION],
            (int) $data[self::AMOUNT],
        );
    }

    public static function makeUpdateCommand(string $id, Request $request): UpdateTransactionCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new UpdateTransactionCommand(
            $id,
            $data[self::TYPE],
            $data[self::SOURCE_WALLET_ID],
            $data[self::TARGET_WALLET_ID],
            $data[self::CATEGORY_ID],
            $data[self::DATE],
            $data[self::DESCRIPTION],
            (int) $data[self::AMOUNT],
        );
    }

    public static function makeDeleteCommand(string $id): DeleteTransactionCommand
    {
        return new DeleteTransactionCommand($id);
    }

    private static function validateRequest(array $data): void
    {
        Assert::lazy()
            ->that($data[self::TYPE] ?? null, self::TYPE)->notEmpty()
            ->that($data[self::SOURCE_WALLET_ID] ?? null, self::SOURCE_WALLET_ID)->notEmpty()->uuid()
            ->that($data[self::TARGET_WALLET_ID] ?? null, self::TARGET_WALLET_ID)->nullOr()->uuid()
            ->that($data[self::CATEGORY_ID] ?? null, self::CATEGORY_ID)->nullOr()->uuid()
            ->that($data[self::DATE] ?? null, self::DATE)->notEmpty()->date('Y-m-d')
            ->that($data[self::DESCRIPTION] ?? null, self::DESCRIPTION)->notEmpty()
            ->that($data[self::AMOUNT] ?? null, self::AMOUNT)->notNull()->integer()
            ->verifyNow();

    }
}

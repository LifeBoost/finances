<?php

declare(strict_types=1);

namespace App\UI\API\Category;

use App\Application\Category\Create\CreateCategoryCommand;
use App\Application\Category\Delete\DeleteCategoryCommand;
use App\Application\Category\Update\UpdateCategoryCommand;
use Assert\Assert;
use Symfony\Component\HttpFoundation\Request;

final class CategoryCommandFactory
{
    private const NAME = 'name';
    private const TYPE = 'type';
    private const ICON = 'icon';

    public static function makeCreateCommand(Request $request): CreateCategoryCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new CreateCategoryCommand(
            $data[self::TYPE],
            $data[self::NAME],
            $data[self::ICON],
        );
    }

    public static function makeUpdateCommand(string $id, Request $request): UpdateCategoryCommand
    {
        $data = $request->toArray();

        self::validateRequest($data);

        return new UpdateCategoryCommand(
            $id,
            $data[self::NAME],
            $data[self::TYPE],
            $data[self::ICON],
        );
    }

    public static function makeDeleteCommand(string $id): DeleteCategoryCommand
    {
        return new DeleteCategoryCommand($id);
    }

    private static function validateRequest(array $data): void
    {
        Assert::lazy()
            ->that($data[self::NAME] ?? null, self::NAME)->notEmpty('Name is required')
            ->that($data[self::TYPE] ?? null, self::TYPE)->notEmpty('Type is required')
            ->that($data[self::ICON] ?? null, self::ICON)->notEmpty('Icon is required')
            ->verifyNow();
    }
}

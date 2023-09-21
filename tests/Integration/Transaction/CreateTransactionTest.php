<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Transaction;

use App\Application\Transaction\Create\CreateTransactionCommand;
use App\Domain\Category\CategoryType;
use App\Domain\Transaction\TransactionType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use App\Tests\Integration\Mother\TransactionMother;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class CreateTransactionTest extends BaseTestCase
{
    public static function createTransactionDataProvider(): array
    {
        return [
            'income transaction with category' => [
                new CreateTransactionCommand(
                    'income',
                    'uuid',
                    null,
                    'uuid',
                    (new DateTimeImmutable())->format('Y-m-d'),
                    'Testing test',
                    1234,
                ),
            ],
            'expense transaction with category' => [
                new CreateTransactionCommand(
                    'expense',
                    'uuid',
                    null,
                    'uuid',
                    (new DateTimeImmutable())->format('Y-m-d'),
                    'Testing test',
                    1234,
                ),
            ],
            'transfer transaction with target wallet' => [
                new CreateTransactionCommand(
                    'transfer',
                    'uuid',
                    'uuid',
                    null,
                    (new DateTimeImmutable())->format('Y-m-d'),
                    'Testing test',
                    1234,
                ),
            ],
        ];
    }

    #[Test]
    #[DataProvider('createTransactionDataProvider')]
    public function tryCreateTransaction(CreateTransactionCommand $command): void
    {
        $sourceWalletId = $this->walletMother->create('Wallet 1', 0, 'PLN')['id'];
        $categoryId = null;
        $targetWalletId = null;

        if ($command->type !== 'transfer') {
            $categoryId = $this->categoryMother->create(CategoryMother::prepareJsonData(CategoryType::from($command->type)->value))['id'];
        } else {
            $targetWalletId = $this->walletMother->create('Wallet 2', 0, 'PLN')['id'];
        }

        $response = $this->post(
            TransactionMother::getUrlPattern(),
            TransactionMother::prepareJsonData(
                $command->type,
                $sourceWalletId,
                $targetWalletId,
                $categoryId,
                $command->date,
                $command->description,
                $command->amount,
            )
        );

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    #[Test]
    public function tryCreateIncomeTransaction(): void
    {
        $sourceWalletId = $this->walletMother->create('Wallet 1', 0, 'PLN')['id'];

        $response = $this->post(
            TransactionMother::getUrlPattern(),
            TransactionMother::prepareJsonData(
                TransactionType::INCOME->value,
                $sourceWalletId,
                null,
                $this->categoryMother->create(CategoryMother::prepareJsonData(CategoryType::INCOME->value))['id'],
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing description',
                1000,
            )
        );

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertTrue(Uuid::isValid($responseData['id']));
    }

    #[Test]
    public function tryCreateTransferTransactionWithCategoryAndWithoutTargetWallet(): void
    {
        $response = $this->post(
            TransactionMother::getUrlPattern(),
            TransactionMother::prepareJsonData(
                TransactionType::TRANSFER->value,
                $this->walletMother->create()['id'],
                null,
                $this->categoryMother->create(CategoryMother::prepareJsonData())['id'],
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing description',
                1234,
            ),
        );

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    #[Test]
    public function tryCreateExpenseTransactionWithoutCategoryAndWithTargetWallet(): void
    {
        $response = $this->post(
            TransactionMother::getUrlPattern(),
            TransactionMother::prepareJsonData(
                TransactionType::EXPENSE->value,
                $this->walletMother->create('Wallet 1')['id'],
                $this->walletMother->create('Wallet 2')['id'],
                null,
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing description',
                1234,
            ),
        );

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function tryCreateTransactionWithSourceWalletFromAnotherUser(): void {}

    public function tryCreateTransactionWithTargetWalletFromAnotherUser(): void {}

    public function tryCreateTransactionWithCategoryFromAnotherUser(): void {}
}

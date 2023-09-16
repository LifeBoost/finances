<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction;

use App\Domain\Transaction\TransactionType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use App\Tests\Integration\Mother\TransactionMother;
use App\Tests\Integration\Mother\WalletMother;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTransactionTest extends BaseTestCase
{
    #[Test]
    public function tryDeleteTransactionWithoutError(): void
    {
        $transaction = $this->transactionMother->create(
            TransactionMother::prepareJsonData(
                TransactionType::INCOME->value,
                $this->walletMother->create()['id'],
                null,
                $this->categoryMother->create(CategoryMother::prepareJsonData())['id'],
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing description',
                1234,
            )
        );

        $response = $this->delete(sprintf('%s/%s', TransactionMother::getUrlPattern(), $transaction['id']));

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    #[Test]
    public function tryDeleteTransactionFromAnotherAccount(): void
    {
        $secondTransactionMother = new TransactionMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));
        $secondWalletMother = new WalletMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));
        $secondCategoryMother = new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));

        $transaction = $secondTransactionMother->create(
            TransactionMother::prepareJsonData(
                TransactionType::INCOME->value,
                $secondWalletMother->create()['id'],
                null,
                $secondCategoryMother->create(CategoryMother::prepareJsonData())['id'],
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing description',
                1234,
            )
        );

        $response = $this->delete(sprintf('%s/%s', TransactionMother::getUrlPattern(), $transaction['id']));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $fetchedTransaction = $secondTransactionMother->getById($transaction['id']);

        self::assertEquals($transaction['id'], $fetchedTransaction['id']);
    }

    #[Test]
    public function tryDeleteNotFoundTransactionAndHaveErrorCode(): void
    {
        $response = $this->delete(sprintf('%s/%s', TransactionMother::getUrlPattern(), Uuid::uuid4()->toString()));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction;

use App\Domain\Transaction\TransactionType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\TransactionMother;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class GetOneByIdTransactionTest extends BaseTestCase
{
    #[Test]
    public function tryGetTransactionWithoutError(): void
    {
        $transaction = $this->transactionMother->create(
            TransactionMother::prepareJsonData(
                TransactionType::INCOME->value,
                $this->walletMother->create()['id'],
                null,
                $this->categoryMother->create()['id'],
                (new DateTimeImmutable())->format('Y-m-d'),
                'Testing name',
                1234,
            )
        );

        $response = $this->get(
            sprintf('%s/%s', TransactionMother::getUrlPattern(), $transaction['id'])
        );

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals($transaction['id'], $responseData['id']);
    }

    public function tryGetTransactionFromAnotherUser(): void
    {
    }

    public function tryGetNotFoundTransaction(): void
    {
    }
}

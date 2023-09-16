<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\TransactionMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class GetAllTransactionTest extends BaseTestCase
{
    #[Test]
    public function tryGetAllTransactionsListWhenEmpty(): void
    {
        $response = $this->get(TransactionMother::getUrlPattern());

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertEmpty($responseData);
    }

    public function tryGetAllTransactionsListWithElements(): void
    {

    }

    public function tryGetAllTransactionsListFromAnotherUser(): void
    {

    }

    public function tryGetAllTransactionsWithDateFrom(): void
    {

    }

    public function tryGetAllTransactionsWithDateTo(): void
    {

    }

    public function tryGetAllTransactionsWithDateFromAndDateTo(): void
    {

    }
}

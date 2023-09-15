<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class GetOneByIdWalletTest extends BaseTestCase
{
    /**
     * @test
     */
    public function getOneByIdWithCorrectData(): void
    {
        $name = 'Wallet 1';
        $startBalance = 120;
        $currency = 'PLN';

        $wallet = $this->walletMother->create($name, $startBalance, $currency);

        $response = $this->get(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']));

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals($wallet['id'], $responseData['id']);
        self::assertEquals($name, $responseData['name']);
        self::assertEquals($startBalance, $responseData['startBalance']);
        self::assertEquals($currency, $responseData['currency']);
    }

    /**
     * @test
     */
    public function tryGetOneByIdFromAnotherUser(): void
    {
        $secondWalletMother = new WalletMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));

        $wallet = $secondWalletMother->create();

        $response = $this->get(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function tryGetOneByIdWithNotFoundId(): void
    {
        $response = $this->get(sprintf('%s/%s', WalletMother::URL_PATTERN, Uuid::uuid4()->toString()));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}

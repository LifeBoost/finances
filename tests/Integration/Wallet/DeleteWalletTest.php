<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class DeleteWalletTest extends BaseTestCase
{
    #[Test]
    public function tryDeleteCorrectWallet(): void
    {
        $wallet = $this->walletMother->create();

        $response = $this->delete(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']));

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    #[Test]
    public function tryDeleteAnotherUserWallet(): void
    {
        $secondWalletMother = new WalletMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));

        $wallet = $secondWalletMother->create();

        $response = $this->delete(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $fetchedWallet = $secondWalletMother->getById($wallet['id']);

        self::assertEquals($wallet['id'], $fetchedWallet['id']);
    }

    #[Test]
    public function tryDeleteNotFoundWallet(): void
    {
        $response = $this->delete(sprintf('%s/%s', WalletMother::URL_PATTERN, Uuid::uuid4()->toString()));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}

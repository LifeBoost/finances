<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;

final class GetAllWalletsTest extends BaseTestCase
{
    /**
     * @test
     */
    public function getAllWalletsWithData(): void
    {
        $this->walletMother->create('Wallet 1');
        $this->walletMother->create('Wallet 2');
        $this->walletMother->create('Wallet 3');

        $data = $this->get(WalletMother::URL_PATTERN);

        self::assertCount(3, $this->parseJson($data->getContent()));
    }

    /**
     * @test
     */
    public function getAllWalletsWithEmptyData(): void
    {
        $data = $this->get(WalletMother::URL_PATTERN);

        self::assertEmpty($this->parseJson($data->getContent()));
    }
}

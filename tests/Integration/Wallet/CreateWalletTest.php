<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;
use Symfony\Component\HttpFoundation\Response;

final class CreateWalletTest extends BaseTestCase
{
    /**
     * @test
     */
    public function createWalletWithDuplicatedName(): void
    {
        $this->walletMother->create('Unique Wallet');

        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Unique Wallet',
            'startBalance' => 120,
            'currency' => 'PLN',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('Wallet with given name already exists', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithoutStartBalance(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Wallet 1',
            'currency' => 'PLN',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('startBalance', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('Start balance is required', $responseData['errors'][0]['message']);
    }
}

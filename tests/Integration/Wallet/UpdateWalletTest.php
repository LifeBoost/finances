<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;
use Symfony\Component\HttpFoundation\Response;

final class UpdateWalletTest extends BaseTestCase
{
    /**
     * @test
     */
    public function updateWithoutAnyError(): void
    {
        $wallet = $this->walletMother->create('Wallet 1', 120, 'PLN');

        $newName = 'New Wallet Name 2';
        $newStartBalance = 200;
        $newCurrency = 'EUR';

        $response = $this->post(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']), [
            'name' => $newName,
            'startBalance' => $newStartBalance,
            'currency' => $newCurrency,
        ]);

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $updated = $this->walletMother->getById($wallet['id']);
        self::assertEquals($newName, $updated['name']);
        self::assertEquals($newStartBalance, $updated['startBalance']);
        self::assertEquals($newCurrency, $updated['currency']);
    }

    /**
     * @test
     */
    public function updateWithErrorOnLongName(): void
    {
        $wallet = $this->walletMother->create('Wallet 1', 120, 'PLN');

        $newName = 'VeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidName';

        $response = $this->post(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']), [
            'name' => $newName,
            'startBalance' => 1234,
            'currency' => 'PLN',
        ]);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('name', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('This value is too long. It should have 255 characters or less.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function updateWithErrorOnInvalidStartBalance(): void
    {
        $wallet = $this->walletMother->create('Wallet 1', 120, 'PLN');

        $newStartBalance = -1234;

        $response = $this->post(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']), [
            'name' => 'Wallet 1',
            'startBalance' => $newStartBalance,
            'currency' => 'EUR',
        ]);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('startBalance', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('This value should be greater than 0.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function updateWithErrorOnInvalidCurrency(): void
    {
        $wallet = $this->walletMother->create('Wallet 1', 120, 'PLN');

        $newCurrency = 'INVALID';

        $response = $this->post(sprintf('%s/%s', WalletMother::URL_PATTERN, $wallet['id']), [
            'name' => 'Wallet 1',
            'startBalance' => 1234,
            'currency' => $newCurrency,
        ]);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('currency', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('The value you selected is not a valid choice.', $responseData['errors'][0]['message']);
    }
}

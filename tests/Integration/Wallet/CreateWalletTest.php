<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Wallet;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\WalletMother;
use Ramsey\Uuid\Uuid;
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
        self::assertEquals(null, $responseData['errors'][0]['propertyPath']);
        self::assertEquals('Failed to create object because the class misses the "startBalance" property.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithoutName(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'startBalance' => 12345,
            'currency' => 'PLN',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals(null, $responseData['errors'][0]['propertyPath']);
        self::assertEquals('Failed to create object because the class misses the "name" property.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithoutCurrency(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Wallet 1',
            'startBalance' => 12345,
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals(null, $responseData['errors'][0]['propertyPath']);
        self::assertEquals('Failed to create object because the class misses the "currency" property.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithoutErrors(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Wallet 1',
            'startBalance' => 1234,
            'currency' => 'PLN',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertArrayHasKey('id', $responseData);
        self::assertTrue(Uuid::isValid($responseData['id']));
    }

    /**
     * @test
     */
    public function createWalletWithInvalidCurrency(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Wallet 1',
            'startBalance' => 123454,
            'currency' => 'test',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('currency', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('The value you selected is not a valid choice.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithInvalidName(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'VeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidNameVeryLongInvalidName',
            'startBalance' => 1234,
            'currency' => 'PLN',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('name', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('This value is too long. It should have 255 characters or less.', $responseData['errors'][0]['message']);
    }

    /**
     * @test
     */
    public function createWalletWithInvalidStartBalance(): void
    {
        $response = $this->post(WalletMother::URL_PATTERN, [
            'name' => 'Wallet 1',
            'startBalance' => -1234,
            'currency' => 'EUR',
        ]);

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('startBalance', $responseData['errors'][0]['propertyPath']);
        self::assertEquals('This value should be greater than 0.', $responseData['errors'][0]['message']);
    }
}

<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Integration\Mother\WalletMother;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseTestCase extends WebTestCase
{
    protected const TEST_JWT_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOiJmMWU0ZjQxZC04ODU1LTRmNjYtYmJlOS03ZGY1ZGNkYjU5NWYifQ.qgG1z3jI3lHtLmXvKVUDB_CcOHrzhsvuF-5xyuBVwOY';

    protected WalletMother $walletMother;

    protected function setUp(): void
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:migrations:migrate -n');

        parent::setUp();

        $this->walletMother = new WalletMother(self::createHttpClient());
    }

    protected function tearDown(): void
    {
        self::runCommand('doctrine:database:drop --force');

        parent::tearDown();
    }

    protected static function getHeaders(): array
    {
        return [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', self::TEST_JWT_TOKEN),
        ];
    }

    protected static function createHttpClient(): KernelBrowser
    {
        self::ensureKernelShutdown();

        return self::createClient([], self::getHeaders());
    }

    public function get(string $url, array $query = []): Response
    {
        $client = self::createHttpClient();
        $client->request(Request::METHOD_GET, $url, $query);

        return $client->getResponse();
    }

    public function post(string $url, array $body = []): Response
    {
        $client = self::createHttpClient();
        $client->jsonRequest(Request::METHOD_POST, $url, $body);

        return $client->getResponse();
    }

    public function put(string $url, array $body = []): Response
    {
        $client = self::createHttpClient();
        $client->jsonRequest(Request::METHOD_PUT, $url, $body);

        return $client->getResponse();
    }

    public function delete(string $url): Response
    {
        $client = self::createHttpClient();
        $client->request(Request::METHOD_DELETE, $url);

        return $client->getResponse();
    }

    protected function parseJson(string $content): array
    {
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    protected static function runCommand($command): int
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication(): Application
    {
        $client = static::createHttpClient();

        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        return $application;
    }
}

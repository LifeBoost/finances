<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Mother;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

final class WalletMother
{
    public const URL_PATTERN = 'api/v1/wallets';

    public function __construct(private readonly KernelBrowser $client){}

    public function create(string $name = 'Wallet 1', int $startBalance = 120, string $currency = 'PLN'): array
    {
        $this->client->restart();

        $this->client->jsonRequest(Request::METHOD_POST, self::URL_PATTERN, [
            'name' => $name,
            'startBalance' => $startBalance,
            'currency' => $currency,
        ]);

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function delete(string $id): void
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_DELETE, sprintf('%s/%s', self::URL_PATTERN, $id));
    }

    public function getAll(): array
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_GET, self::URL_PATTERN);

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function deleteAll(): void
    {
        foreach ($this->getAll() as $wallet) {
            $this->delete($wallet['id']);
        }
    }

    public function getById(string $id): array
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_GET, sprintf('%s/%s', self::URL_PATTERN, $id));

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Integration\Mother;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

abstract readonly class AbstractMother
{
    abstract public static function getUrlPattern(): string;

    public function __construct(private KernelBrowser $client)
    {
    }

    /**
     * @throws JsonException
     */
    public function create(?array $jsonData = null): array
    {
        $this->client->restart();

        $this->client->jsonRequest(Request::METHOD_POST, static::getUrlPattern(), $jsonData ?? CategoryMother::prepareJsonData());

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function delete(string $id): void
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_DELETE, sprintf('%s/%s', static::getUrlPattern(), $id));
    }

    /**
     * @throws JsonException
     */
    public function getAll(): array
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_GET, static::getUrlPattern());

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function deleteAll(): void
    {
        foreach ($this->getAll() as $item) {
            $this->delete($item['id']);
        }
    }

    /**
     * @throws JsonException
     */
    public function getById(string $id): array
    {
        $this->client->restart();

        $this->client->request(Request::METHOD_GET, sprintf('%s/%s', static::getUrlPattern(), $id));

        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}

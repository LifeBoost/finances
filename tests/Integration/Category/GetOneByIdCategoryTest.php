<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Domain\Category\CategoryType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class GetOneByIdCategoryTest extends BaseTestCase
{
    #[Test]
    public function tryGetCategoryOneByIdWithoutError(): void
    {
        $type = CategoryType::INCOME->value;
        $name = 'Unique name';
        $icon = 'dashboard';

        $category = $this->categoryMother->create(CategoryMother::prepareJsonData($type, $name, $icon));

        $response = $this->get(sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']));

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertEquals($category['id'], $responseData['id']);
        self::assertEquals($type, $responseData['type']);
        self::assertEquals($name, $responseData['name']);
        self::assertEquals($icon, $responseData['icon']);
    }

    #[Test]
    public function tryGetCategoryByIdWithNotFoundId(): void
    {
        $response = $this->get(sprintf('%s/%s', CategoryMother::getUrlPattern(), Uuid::uuid4()->toString()));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    #[Test]
    public function tryGetCategoryByIdWithCategoryOnAnotherUser(): void
    {
        $category = (new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN)))->create(CategoryMother::prepareJsonData());

        $response = $this->get(sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}

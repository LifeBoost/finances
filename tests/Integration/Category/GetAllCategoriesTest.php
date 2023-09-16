<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class GetAllCategoriesTest extends BaseTestCase
{
    #[Test]
    public function tryFetchAllCategoriesWithEmptyList(): void
    {
        $response = $this->get(CategoryMother::getUrlPattern());

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $this->parseJson($response->getContent());

        self::assertCount(0, $responseData);
    }

    #[Test]
    public function tryFetchAllCategoriesWithNotEmptyList(): void
    {
        $this->categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 1'));
        $this->categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 2'));
        $this->categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 3'));

        $response = $this->get(CategoryMother::getUrlPattern());

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $this->parseJson($response->getContent());

        self::assertCount(3, $responseData);
    }

    #[Test]
    public function tryFetchAllCategoriesStoredOnAnotherUser(): void
    {
        $categoryMother = new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));
        $categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 1'));
        $categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 2'));
        $categoryMother->create(CategoryMother::prepareJsonData(name: 'Category 3'));

        $response = $this->get(CategoryMother::getUrlPattern());

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $this->parseJson($response->getContent());

        self::assertCount(0, $responseData);
    }
}

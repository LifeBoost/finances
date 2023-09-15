<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class DeleteCategoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function tryDeleteCategoryWithoutError(): void
    {
        $category = $this->categoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->delete(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id'])
        );

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function tryDeleteNotFoundCategory(): void
    {
        $response = $this->delete(sprintf('%s/%s', CategoryMother::getUrlPattern(), Uuid::uuid4()->toString()));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function tryDeleteAnotherUsersCategory(): void
    {
        $secondCategoryMother = new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));
        $category = $secondCategoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->delete(sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']));

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $fetchedCategory = $secondCategoryMother->getById($category['id']);

        self::assertEquals($category['id'], $fetchedCategory['id']);
    }
}

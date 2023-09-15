<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace App\Tests\Integration\Category;

use App\Domain\Category\CategoryType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\CategoryMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class UpdateCategoryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function tryUpdateCategoryWithoutError(): void
    {
        $newType = CategoryType::EXPENSE->value;
        $newName = 'New unique name after edit';
        $newIcon = 'testing test';

        $category = $this->categoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData($newType, $newName, $newIcon),
        );

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $category = $this->categoryMother->getById($category['id']);

        self::assertEquals($newType, $category['type']);
        self::assertEquals($newName, $category['name']);
        self::assertEquals($newIcon, $category['icon']);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryForAnotherUser(): void
    {
        $secondCategoryMother = new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));

        $oldType = CategoryType::EXPENSE->value;
        $oldName = 'Unique name';
        $oldIcon = 'dashboard';

        $category = $secondCategoryMother->create(
            CategoryMother::prepareJsonData($oldType, $oldName, $oldIcon)
        );

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData(),
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $category = $secondCategoryMother->getById($category['id']);

        self::assertEquals($oldType, $category['type']);
        self::assertEquals($oldName, $category['name']);
        self::assertEquals($oldIcon, $category['icon']);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryToDuplicatedNameAndType(): void
    {
        $type = CategoryType::INCOME->value;
        $name = 'Unique name';

        $category = $this->categoryMother->create(CategoryMother::prepareJsonData($type, $name));

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData($type, $name),
        );

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryWithInvalidType(): void
    {
        $category = $this->categoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData('invalid_type'),
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(1, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('type', $responseData['errors'][0]['propertyPath']);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryWithInvalidName(): void
    {
        $category = $this->categoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData(name: ''),
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(2, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('name', $responseData['errors'][0]['propertyPath']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][1]);
        self::assertEquals('name', $responseData['errors'][1]['propertyPath']);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryWithInvalidIcon(): void
    {
        $category = $this->categoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData(icon: ''),
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(1, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('icon', $responseData['errors'][0]['propertyPath']);
    }

    /**
     * @test
     */
    public function tryUpdateNotFoundCategory(): void
    {
        $response = $this->post(sprintf('%s/%s', CategoryMother::getUrlPattern(), Uuid::uuid4()->toString()), CategoryMother::prepareJsonData());

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
    }

    /**
     * @test
     */
    public function tryUpdateCategoryOnlyIcon(): void
    {
        $type = CategoryType::INCOME->value;
        $name = 'Unique name';
        $icon = 'dashboard';
        $newIcon = 'testing';

        $category = $this->categoryMother->create(
            CategoryMother::prepareJsonData($type, $name, $icon)
        );

        $response = $this->post(
            sprintf('%s/%s', CategoryMother::getUrlPattern(), $category['id']),
            CategoryMother::prepareJsonData($type, $name, $newIcon)
        );

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}

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

final class CreateCategoryTest extends BaseTestCase
{
    #[Test]
    public function tryCreateWithoutError(): void
    {
        $type = CategoryType::EXPENSE->value;
        $name = 'Unique category name';
        $icon = 'dashboard';

        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData($type, $name, $icon));

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertTrue(Uuid::isValid($responseData['id']));
    }

    #[Test]
    public function tryCreateWithInvalidType(): void
    {
        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData('invalid'));

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(1, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('type', $responseData['errors'][0]['propertyPath']);
    }

    #[Test]
    public function tryCreateWithInvalidName(): void
    {
        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData(name: ''));

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(2, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('name', $responseData['errors'][0]['propertyPath']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][1]);
        self::assertEquals('name', $responseData['errors'][1]['propertyPath']);
    }

    #[Test]
    public function tryCreateWithInvalidIcon(): void
    {
        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData(icon: ''));

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
        self::assertCount(1, $responseData['errors']);
        self::assertArrayHasKey('propertyPath', $responseData['errors'][0]);
        self::assertEquals('icon', $responseData['errors'][0]['propertyPath']);
    }

    #[Test]
    public function tryCreateWithDuplicatedNameAndDifferentTypes(): void
    {
        $name = 'Unique name';

        $response = $this->post(
            CategoryMother::getUrlPattern(),
            CategoryMother::prepareJsonData(CategoryType::INCOME->value, $name)
        );

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertTrue(Uuid::isValid($responseData['id']));

        $response = $this->post(
            CategoryMother::getUrlPattern(),
            CategoryMother::prepareJsonData(CategoryType::EXPENSE->value, $name)
        );

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertTrue(Uuid::isValid($responseData['id']));
    }

    #[Test]
    public function tryCreateWithDuplicatedNameAndTypes(): void
    {
        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData());

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertTrue(Uuid::isValid($responseData['id']));

        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData());

        self::assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());

        self::assertArrayHasKey('errors', $responseData);
    }

    #[Test]
    public function tryCreateWithDuplicatedNameOnDifferentUsers(): void
    {
        $secondCategoryMother = new CategoryMother(self::createHttpClient(self::SECOND_TEST_JWT_TOKEN));

        $secondCategoryMother->create(CategoryMother::prepareJsonData());

        $response = $this->post(CategoryMother::getUrlPattern(), CategoryMother::prepareJsonData());

        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = $this->parseJson($response->getContent());


        self::assertTrue(Uuid::isValid($responseData['id']));
    }
}

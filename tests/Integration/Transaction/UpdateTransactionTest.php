<?php

declare(strict_types=1);

namespace App\Tests\Integration\Transaction;

use App\Domain\Transaction\TransactionType;
use App\Tests\Integration\BaseTestCase;
use App\Tests\Integration\Mother\TransactionMother;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

final class UpdateTransactionTest extends BaseTestCase
{
    #[Test]
    public function tryUpdateTransactionDescriptionWithoutError(): void
    {
        $categoryId = $this->categoryMother->create()['id'];
        $sourceWalletId = $this->walletMother->create()['id'];
        $type = TransactionType::INCOME->value;
        $date = (new DateTimeImmutable())->format('Y-m-d');
        $oldDescription = 'Testing description';
        $newDescription = 'New testing description';
        $amount = 1234;

        $transaction = $this->transactionMother->create(
            TransactionMother::prepareJsonData(
                $type,
                $sourceWalletId,
                null,
                $categoryId,
                $date,
                $oldDescription,
                $amount,
            )
        );

        $response = $this->post(
            sprintf('%s/%s', TransactionMother::getUrlPattern(), $transaction['id']),
            TransactionMother::prepareJsonData(
                $type,
                $sourceWalletId,
                null,
                $categoryId,
                $date,
                $newDescription,
                $amount,
            ),
        );

        self::assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $transaction = $this->transactionMother->getById($transaction['id']);

        self::assertEquals($newDescription, $transaction['description']);
    }

    public function tryUpdateTransactionWithInvalidType(): void {}

    public function tryUpdateExpenseTransactionWithTargetWallet(): void {}

    public function tryUpdateTransferTransactionWithCategory(): void {}

    public function tryUpdateTransactionWithZeroAmount(): void {}

    public function tryUpdateTransactionWithSourceWalletFromAnotherUser(): void {}

    public function tryUpdateTransferTransactionWithTargetWalletFromAnotherUser(): void {}

    public function tryUpdateExpenseTransactionWithCategoryFromAnotherUser(): void {}
}

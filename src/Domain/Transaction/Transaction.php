<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Category\Category;
use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use App\Domain\Wallet\Wallet;
use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Id;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: 'transactions')]
class Transaction
{
    /**
     * @throws DomainException
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private Id $id,
        #[ORM\Column(type: 'uuid_user')]
        private readonly UserId $userId,
        #[ORM\Column]
        private string $type,
        #[ORM\ManyToOne(targetEntity: Wallet::class)]
        #[ORM\JoinColumn(name: 'source_wallet_id', referencedColumnName: 'id')]
        private Wallet $sourceWallet,
        #[ORM\ManyToOne(targetEntity: Wallet::class)]
        #[ORM\JoinColumn(name: 'target_wallet_id', referencedColumnName: 'id')]
        private ?Wallet $targetWallet,
        #[ORM\ManyToOne(targetEntity: Category::class)]
        #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
        private ?Category $category,
        #[ORM\Column(type: 'date_immutable')]
        private DateTimeImmutable $date,
        #[ORM\Column(length: 255)]
        private string $description,
        #[ORM\Column]
        private int $amount,
    ) {
        $this->validateType(
            TransactionType::from($this->type),
            $this->targetWallet,
            $this->category,
        );
    }

    /**
     * @throws DomainException
     */
    public static function create(
        TransactionType $type,
        Wallet $sourceWallet,
        ?Wallet $targetWallet,
        ?Category $category,
        DateTimeImmutable $date,
        string $description,
        int $amount,
        UserContext $userContext,
    ): self {
        return new self(
            Id::generate(),
            $userContext->getUserId(),
            $type->value,
            $sourceWallet,
            $targetWallet,
            $category,
            $date,
            $description,
            $amount,
        );
    }

    /**
     * @throws DomainException
     */
    public function update(
        TransactionType $type,
        Wallet $sourceWallet,
        ?Wallet $targetWallet,
        ?Category $category,
        DateTimeImmutable $date,
        string $description,
        int $amount
    ): void {
        $this->validateType($type, $targetWallet, $category);

        $this->type = $type->value;
        $this->sourceWallet = $sourceWallet;
        $this->targetWallet = $targetWallet;
        $this->category = $category;
        $this->date = $date;
        $this->description = $description;
        $this->amount = $amount;
    }

    /**
     * @throws DomainException
     */
    private function validateType(TransactionType $type, ?Wallet $targetWalletId, ?Category $categoryId): void
    {
        if ($type === TransactionType::TRANSFER && $targetWalletId === null) {
            throw new DomainException('For transfer type target wallet must be provided');
        }

        if ($type !== TransactionType::TRANSFER && $categoryId === null) {
            throw new DomainException(sprintf('For %s type category must be provided', $type->value));
        }
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getType(): TransactionType
    {
        return TransactionType::from($this->type);
    }

    public function getSourceWallet(): Wallet
    {
        return $this->sourceWallet;
    }

    public function getTargetWallet(): ?Wallet
    {
        return $this->targetWallet;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}

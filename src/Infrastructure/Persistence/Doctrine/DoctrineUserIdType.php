<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\User\UserId;
use App\SharedKernel\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class DoctrineUserIdType extends GuidType
{
    public function getName(): string
    {
        return 'uuid_user';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        if ($value instanceof UserId) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        return UserId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof UserId) {
            return $value->toString();
        }

        return $value;
    }
}

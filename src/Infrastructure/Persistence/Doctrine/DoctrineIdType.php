<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\SharedKernel\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class DoctrineIdType extends GuidType
{
    public function getName(): string
    {
        return 'uuid';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        if ($value instanceof Id) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        return Id::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Id) {
            return $value->toString();
        }

        return $value;
    }
}

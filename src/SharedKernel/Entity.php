<?php

declare(strict_types=1);

namespace App\SharedKernel;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
abstract class Entity
{
    #[ORM\Column(type: 'uuid')]
    protected ?UuidInterface $userId = null;

    public function setUserId(UuidInterface $userId): void
    {
        $this->userId = $userId;
    }
}

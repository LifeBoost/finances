<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\User\UserContext;
use App\SharedKernel\Entity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Ramsey\Uuid\Uuid;

#[AsDoctrineListener(event: Events::prePersist)]
final readonly class AbstractEntityEventSubscriber
{
    public function __construct(
        private UserContext $userContext,
    ) {
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Entity) {
            $entity->setUserId(
                Uuid::fromString($this->userContext->getUserId()->toString())
            );
        }
    }
}

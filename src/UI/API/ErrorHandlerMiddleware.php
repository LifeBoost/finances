<?php

declare(strict_types=1);

namespace App\UI\API;

use App\SharedKernel\Exception\DomainException;
use Assert\InvalidArgumentException;
use Assert\LazyAssertionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;

final class ErrorHandlerMiddleware implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse(
                new JsonResponse([
                    'errors' => array_map(
                        static fn (ConstraintViolation $violation) => [
                            'propertyPath' => $violation->getPropertyPath(),
                            'message' => $violation->getMessage()
                        ],
                        $exception->getViolations()->getIterator()->getArrayCopy()
                    ),
                ], Response::HTTP_BAD_REQUEST),
            );

            return;
        }

        if ($exception instanceof LazyAssertionException) {
            $event->setResponse(
                new JsonResponse([
                    'errors' => array_map(
                        static fn (InvalidArgumentException $violation) => [
                            'propertyPath' => $violation->getPropertyPath(),
                            'message' => $violation->getMessage(),
                        ],
                        $exception->getErrorExceptions()
                    )
                ], Response::HTTP_BAD_REQUEST)
            );

            return;
        }

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();

            if ($exception instanceof DomainException) {
                $event->setResponse(
                    new JsonResponse([
                        'errors' => [
                            'message' => $exception->getMessage(),
                        ]
                    ], Response::HTTP_CONFLICT)
                );

                return;
            }

            return;
        }


    }
}
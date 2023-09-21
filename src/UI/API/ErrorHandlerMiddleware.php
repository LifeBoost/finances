<?php

declare(strict_types=1);

namespace App\UI\API;

use App\SharedKernel\Exception\DomainException;
use App\SharedKernel\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException as SymfonyValidatorValidationFailedException;

final class ErrorHandlerMiddleware implements EventSubscriberInterface
{
    private const SYMFONY_VALIDATION_PARAMETERS_TYPE_KEY = '{{ type }}';
    private const SYMFONY_VALIDATION_PARAMETERS_HINT_KEY = 'hint';
    private const SYMFONY_VALIDATION_UNKNOWN_TYPE = 'unknown';

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
                            'message' => $violation->getMessage(),
                        ],
                        $exception->getViolations()->getIterator()->getArrayCopy()
                    ),
                ], Response::HTTP_BAD_REQUEST),
            );

            return;
        }

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();

            if ($exception instanceof DomainException) {
                $event->setResponse(
                    new JsonResponse([
                        'errors' => [
                            [
                                'message' => $exception->getMessage(),
                            ],
                        ],
                    ], Response::HTTP_CONFLICT)
                );

                return;
            }

            if ($exception instanceof NotFoundException) {
                $event->setResponse(
                    new JsonResponse([
                        'errors' => [
                            [
                                'message' => $exception->getMessage(),
                            ],
                        ],
                    ], Response::HTTP_NOT_FOUND)
                );

                return;
            }

            return;
        }

        if (
            $exception instanceof HttpException
            && $exception->getPrevious() instanceof SymfonyValidatorValidationFailedException
        ) {
            /** @var SymfonyValidatorValidationFailedException $previous */
            $previous = $exception->getPrevious();
            $errors = [];

            foreach ($previous->getViolations() as $violation) {
                $parameters = $violation->getParameters();

                if (
                    isset($parameters[self::SYMFONY_VALIDATION_PARAMETERS_TYPE_KEY], $parameters[self::SYMFONY_VALIDATION_PARAMETERS_HINT_KEY])
                    && $parameters[self::SYMFONY_VALIDATION_PARAMETERS_TYPE_KEY] === self::SYMFONY_VALIDATION_UNKNOWN_TYPE
                ) {
                    $errors = [
                        [
                            'propertyPath' => null,
                            'message' => $violation->getParameters()[self::SYMFONY_VALIDATION_PARAMETERS_HINT_KEY],
                        ],
                    ];

                    break;
                }

                $errors[] = [
                    'propertyPath' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $event->setResponse(
                new JsonResponse([
                    'errors' => $errors,
                ], Response::HTTP_BAD_REQUEST),
            );

            return;
        }
    }
}

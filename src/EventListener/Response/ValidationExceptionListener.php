<?php

declare(strict_types=1);

namespace App\EventListener\Response;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: ExceptionEvent::class, method: 'onException')]
final class ValidationExceptionListener
{
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof UnprocessableEntityHttpException
            && $exception->getPrevious() instanceof ValidationFailedException
            && 'application/json' === $event->getRequest()->headers->get('Accept', '')
        ) {
            /** @var ValidationFailedException $previousException */
            $previousException = $exception->getPrevious();

            $violations = [];

            foreach ($previousException->getViolations() as $violation) {
                $violations[] = ['property' => $violation->getPropertyPath(), 'message' => $violation->getMessage()];
            }

            $event->setResponse(
                new JsonResponse(
                    data: ['violations' => $violations],
                    status: $exception->getStatusCode(),
                ),
            );
        }
    }
}
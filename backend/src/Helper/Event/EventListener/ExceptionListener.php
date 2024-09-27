<?php

namespace App\Helper\Event\EventListener;

use App\Helper\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\{Event\ExceptionEvent, Exception\HttpException};

final readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error($exception->getMessage(), $exception->getTrace());
        if ($exception instanceof ApiException) {
            $errorJsonResponse = new JsonResponse($exception->getResponseBody(), $exception->getStatusCode());
        } elseif ($exception instanceof HttpException) {
            $ex = new ApiException(message: $exception->getMessage(), status: $exception->getStatusCode());
            $errorJsonResponse = new JsonResponse($ex->getResponseBody(), $ex->getStatusCode());
        } else {
            return;
        }
        $event->setResponse($errorJsonResponse);
    }
}

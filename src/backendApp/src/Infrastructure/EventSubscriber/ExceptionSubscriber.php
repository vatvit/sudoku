<?php

namespace App\Infrastructure\EventSubscriber;

use App\Domain\Sudoku\Exception\GameAlreadyCompletedException;
use App\Domain\Sudoku\Exception\GameNotFoundException;
use App\Domain\Sudoku\Exception\InvalidMoveException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionSubscriber implements EventSubscriberInterface
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
        $request = $event->getRequest();

        // Only handle API requests (requests that expect JSON)
        if (!$this->isApiRequest($request)) {
            return;
        }

        $response = $this->createErrorResponse($exception);
        $event->setResponse($response);
    }

    private function isApiRequest($request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/') || 
               $request->headers->get('Accept') === 'application/json' ||
               $request->headers->get('Content-Type') === 'application/json';
    }

    private function createErrorResponse(\Throwable $exception): JsonResponse
    {
        $statusCode = 500;
        $errorCode = 'INTERNAL_SERVER_ERROR';
        $title = 'Internal Server Error';
        $detail = 'An unexpected error occurred';

        if ($exception instanceof ValidationFailedException) {
            $statusCode = 400;
            $errorCode = 'VALIDATION_FAILED';
            $title = 'Validation Failed';
            $detail = 'The request data is invalid';

            return $this->createValidationErrorResponse($exception, $statusCode, $title, $detail);
        }

        // Handle specific business logic exceptions
        if ($exception instanceof GameNotFoundException) {
            $statusCode = 404;
            $errorCode = 'GAME_NOT_FOUND';
            $title = 'Game Not Found';
            $detail = $exception->getMessage();
        } elseif ($exception instanceof InvalidMoveException) {
            $statusCode = 400;
            $errorCode = 'INVALID_MOVE';
            $title = 'Invalid Move';
            $detail = $exception->getMessage();
        } elseif ($exception instanceof GameAlreadyCompletedException) {
            $statusCode = 409;
            $errorCode = 'GAME_ALREADY_COMPLETED';
            $title = 'Game Already Completed';
            $detail = $exception->getMessage();
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $errorCode = 'NOT_FOUND';
            $title = 'Not Found';
            $detail = $exception->getMessage() ?: 'The requested resource was not found';
        } elseif ($exception instanceof BadRequestHttpException) {
            $statusCode = 400;
            $errorCode = 'BAD_REQUEST';
            $title = 'Bad Request';
            $detail = $exception->getMessage() ?: 'The request is invalid';
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $errorCode = $this->getErrorCodeForHttpStatus($statusCode);
            $title = $this->getTitleForHttpStatus($statusCode);
            $detail = $exception->getMessage() ?: $this->getDetailForHttpStatus($statusCode);
        }

        $errorData = [
            'type' => sprintf('https://example.com/errors/%s', strtolower(str_replace('_', '-', $errorCode))),
            'title' => $title,
            'status' => $statusCode,
            'detail' => $detail,
            'code' => $errorCode,
        ];

        return new JsonResponse($errorData, $statusCode);
    }

    private function createValidationErrorResponse(ValidationFailedException $exception, int $statusCode, string $title, string $detail): JsonResponse
    {
        $violations = [];
        foreach ($exception->getViolations() as $violation) {
            $violations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'code' => $violation->getCode() ?: 'VALIDATION_ERROR',
            ];
        }

        $errorData = [
            'type' => 'https://symfony.com/errors/validation',
            'title' => $title,
            'status' => $statusCode,
            'detail' => $detail,
            'code' => 'VALIDATION_FAILED',
            'violations' => $violations,
        ];

        return new JsonResponse($errorData, $statusCode);
    }

    private function getErrorCodeForHttpStatus(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            409 => 'CONFLICT',
            422 => 'UNPROCESSABLE_ENTITY',
            429 => 'TOO_MANY_REQUESTS',
            500 => 'INTERNAL_SERVER_ERROR',
            502 => 'BAD_GATEWAY',
            503 => 'SERVICE_UNAVAILABLE',
            504 => 'GATEWAY_TIMEOUT',
            default => 'HTTP_ERROR_' . $statusCode,
        };
    }

    private function getTitleForHttpStatus(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            default => 'HTTP Error',
        };
    }

    private function getDetailForHttpStatus(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'The request is invalid',
            401 => 'Authentication is required',
            403 => 'Access is forbidden',
            404 => 'The requested resource was not found',
            405 => 'The HTTP method is not allowed',
            409 => 'The request conflicts with the current state',
            422 => 'The request data is unprocessable',
            429 => 'Too many requests have been made',
            500 => 'An unexpected error occurred',
            502 => 'Bad gateway',
            503 => 'The service is temporarily unavailable',
            504 => 'Gateway timeout',
            default => 'An HTTP error occurred',
        };
    }
}

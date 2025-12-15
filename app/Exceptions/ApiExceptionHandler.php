<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ApiExceptionHandler
{
    /**
     * @param Throwable $e
     * @param Request $request
     * @return JsonResponse|null
     */
    public static function handle(Throwable $e, Request $request): JsonResponse|null
    {
        if (!$request->is('api/*')) {
            return null;
        }

        [$statusCode, $message] = self::resolveException($e);

        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->errors();
        }

        if ($e instanceof QueryException && !config('app.debug')) {
            $response['message'] = 'Database error occurred';
        }

        if (config('app.debug')) {
            $response['debug'] = self::getDebugData($e);
        }

        $response['data'] = null;

        return response()->json($response, $statusCode);
    }

    private static function resolveException(Throwable $e): array
    {
        return match (true) {
            $e instanceof ModelNotFoundException => [404, 'Resource not found'],
            $e instanceof NotFoundHttpException => [
                404,
                str_contains($e->getMessage(), 'No query results')
                ? 'Resource not found'
                : 'Endpoint not found',
            ],
            $e instanceof MethodNotAllowedHttpException => [405, 'Method not allowed'],
            $e instanceof ValidationException => [422, 'Validation failed'],
            $e instanceof AuthenticationException => [401, 'Unauthorized'],
            $e instanceof AuthorizationException => [403, 'Forbidden'],
            $e instanceof TooManyRequestsHttpException => [429, 'Too many requests'],
            $e instanceof QueryException => [500, 'Database error'],
            $e instanceof HttpException => [$e->getStatusCode(), $e->getMessage() ?: 'An error occurred'],
            default => [
                500,
                config('app.debug')
                ? $e->getMessage()
                : 'An unexpected error occurred',
            ],
        };
    }

    private static function getDebugData(Throwable $e): array
    {
        return [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
    }
}

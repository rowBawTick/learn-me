<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CustomLogger
{
    /**
     * Log an info message with context
     */
    public static function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * Log a debug message with context
     */
    public static function debug(string $message, array $context = []): void
    {
        Log::debug($message, $context);
    }

    /**
     * Log a warning message with context
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    /**
     * Log an error message with context
     */
    public static function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * Log an error and return a JSON response
     */
    public static function errorResponse(
        string $message,
        \Exception $exception,
        array $context = [],
        int $statusCode = 500
    ): JsonResponse {
        self::error($message, array_merge([
            'error' => $exception->getMessage(),
        ], $context));

        if (config('app.debug')) {
            return response()->json([
                'message' => $message,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ], $statusCode);
        }

        return response()->json(['message' => $message], $statusCode);
    }
}

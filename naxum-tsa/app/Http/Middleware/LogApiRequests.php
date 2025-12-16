<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * Log API request details in structured JSON format including:
     * - Request metadata (ID, timestamp, method, path)
     * - Client information (IP, user agent)
     * - Performance metrics (duration, memory usage)
     * - Response details (status code, content length)
     *
     * This middleware provides visibility into API usage patterns and performance.
     * Logs are stored in JSON format for easy parsing by log aggregation tools.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $requestId = $this->generateRequestId();
        $timestamp = now()->toIso8601String();

        // Add request ID to the request for tracing throughout the application
        $request->headers->set('X-Request-ID', $requestId);

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsed = memory_get_usage(true) - $startMemory;

        // Structured log entry for JSON formatting
        Log::channel('api')->info('api_request', [
            'request' => [
                'id' => $requestId,
                'timestamp' => $timestamp,
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'query_params' => $request->query() ?: null,
                'route_name' => $request->route()?->getName(),
            ],
            'client' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'response' => [
                'status_code' => $response->getStatusCode(),
                'status_text' => Response::$statusTexts[$response->getStatusCode()] ?? 'Unknown',
                'content_length' => strlen($response->getContent() ?: ''),
            ],
            'performance' => [
                'duration_ms' => $duration,
                'memory_bytes' => $memoryUsed,
                'memory_peak_bytes' => memory_get_peak_usage(true),
            ],
            'environment' => [
                'app_env' => config('app.env'),
                'php_version' => PHP_VERSION,
            ],
        ]);

        // Add request ID to response headers for client-side tracing
        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-Response-Time', $duration.'ms');

        return $response;
    }

    /**
     * Generate a unique request ID.
     *
     * Format: req_<timestamp>_<random>
     * This format ensures uniqueness and sortability by time.
     */
    private function generateRequestId(): string
    {
        return sprintf(
            'req_%s_%s',
            now()->format('YmdHis'),
            bin2hex(random_bytes(4))
        );
    }
}

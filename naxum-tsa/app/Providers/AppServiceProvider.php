<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureQueryLogging();
    }

    /**
     * Configure database query logging.
     *
     * Logs queries in structured JSON format for easy parsing and analysis.
     * Slow queries (over threshold) are logged as warnings.
     * In debug mode, all queries are logged.
     */
    private function configureQueryLogging(): void
    {
        // Only enable query logging if configured
        if (! config('app.debug') && ! config('logging.log_queries', false)) {
            return;
        }

        DB::listen(function (QueryExecuted $query): void {
            $slowQueryThreshold = (float) config('logging.slow_query_threshold', 100);
            $isSlow = $query->time > $slowQueryThreshold;

            // Build structured log entry
            $logEntry = [
                'query' => [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'connection' => $query->connectionName,
                ],
                'performance' => [
                    'time_ms' => round($query->time, 2),
                    'is_slow' => $isSlow,
                    'threshold_ms' => $slowQueryThreshold,
                ],
                'context' => [
                    'timestamp' => now()->toIso8601String(),
                    'request_id' => request()->header('X-Request-ID'),
                ],
            ];

            // Log slow queries as warnings, others as debug
            if ($isSlow) {
                Log::channel('queries')->warning('slow_query', $logEntry);
            } elseif (config('app.debug')) {
                Log::channel('queries')->debug('query', $logEntry);
            }
        });
    }
}

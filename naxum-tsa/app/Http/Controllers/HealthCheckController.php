<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    /**
     * Check application health status.
     *
     * Returns the status of the application and database connection.
     */
    public function __invoke(): JsonResponse
    {
        $databaseStatus = $this->checkDatabaseConnection();

        $status = $databaseStatus ? 'healthy' : 'unhealthy';
        $httpCode = $databaseStatus ? 200 : 503;

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => $databaseStatus ? 'connected' : 'disconnected',
            ],
        ], $httpCode);
    }

    /**
     * Check database connection.
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}




<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class OrderNotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(int $orderId)
    {
        parent::__construct("Order with ID {$orderId} not found.");
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 404);
    }
}




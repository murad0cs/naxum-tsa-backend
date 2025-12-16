<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidFilterException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Invalid filter parameters provided.')
    {
        parent::__construct($message);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 400);
    }
}




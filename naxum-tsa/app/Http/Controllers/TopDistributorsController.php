<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TopDistributorsRequest;
use App\Services\TopDistributorsService;
use Illuminate\Http\JsonResponse;

class TopDistributorsController extends Controller
{
    private const TOP_DISTRIBUTORS_LIMIT = 200;

    public function __construct(
        private readonly TopDistributorsService $service
    ) {}

    /**
     * Get top 200 distributors by total sales.
     */
    public function index(TopDistributorsRequest $request): JsonResponse
    {
        $result = $this->service->getTopDistributorsPaginated(
            self::TOP_DISTRIBUTORS_LIMIT,
            $request->getPerPage(),
            $request->getPage()
        );

        // Format monetary values to always show 2 decimal places
        $formattedData = array_map(function ($item) {
            $item['total_sales'] = number_format((float) $item['total_sales'], 2, '.', '');

            return $item;
        }, $result['data']);

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'pagination' => $result['pagination'],
        ]);
    }
}

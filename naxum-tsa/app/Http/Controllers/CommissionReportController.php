<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\OrderNotFoundException;
use App\Http\Requests\CommissionReportRequest;
use App\Services\CommissionReportService;
use Illuminate\Http\JsonResponse;

class CommissionReportController extends Controller
{
    public function __construct(
        private readonly CommissionReportService $service
    ) {}

    /**
     * Get the commission report with optional filters.
     *
     * Returns a paginated list of orders with commission details.
     * Supports filtering by distributor, date range, and invoice number.
     */
    public function index(CommissionReportRequest $request): JsonResponse
    {
        $report = $this->service->getReport(
            $request->getFilters(),
            $request->getPerPage()
        );

        // Format monetary values to always show 2 decimal places
        $formattedData = array_map(function ($item) {
            $item['order_total'] = number_format((float) $item['order_total'], 2, '.', '');
            $item['commission'] = number_format((float) $item['commission'], 2, '.', '');

            return $item;
        }, $report['data']);

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'pagination' => $report['pagination'],
        ]);
    }

    /**
     * Get order items for a specific order.
     *
     * Returns the list of products included in the specified order.
     *
     * @throws OrderNotFoundException
     */
    public function orderItems(int $orderId): JsonResponse
    {
        $items = $this->service->getOrderItems($orderId);

        if (empty($items)) {
            throw new OrderNotFoundException($orderId);
        }

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}

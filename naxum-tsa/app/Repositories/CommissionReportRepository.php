<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\CommissionReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CommissionReportRepository implements CommissionReportRepositoryInterface
{
    /**
     * Category ID for Distributor users.
     */
    private const CATEGORY_DISTRIBUTOR = 1;

    /**
     * Category ID for Customer users.
     */
    private const CATEGORY_CUSTOMER = 2;

    /**
     * Commission percentage tiers based on referred distributors count.
     *
     * @var array<int, array{min: int, max: int, percentage: int}>
     */
    private const COMMISSION_TIERS = [
        ['min' => 0, 'max' => 4, 'percentage' => 5],
        ['min' => 5, 'max' => 10, 'percentage' => 10],
        ['min' => 11, 'max' => 20, 'percentage' => 15],
        ['min' => 21, 'max' => 29, 'percentage' => 20],
        ['min' => 30, 'max' => PHP_INT_MAX, 'percentage' => 30],
    ];

    /**
     * Get the commission report with filters.
     *
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getCommissionReport(array $filters = [], int $perPage = 10): array
    {
        $page = (int) ($filters['page'] ?? 1);
        $offset = ($page - 1) * $perPage;

        $query = $this->buildBaseQuery();
        $this->applyFilters($query, $filters);

        $total = (clone $query)->count();

        $orders = $query
            ->orderBy('o.order_date', 'desc')
            ->orderBy('o.invoice_number')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $results = $this->processOrders($orders);

        return [
            'data' => $results,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Get order items for a specific order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrderItems(int $orderId): array
    {
        $items = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('oi.order_id', '=', $orderId)
            ->select([
                'p.sku',
                'p.name as product_name',
                'p.price',
                'oi.quantity',
                DB::raw('ROUND(p.price * oi.quantity, 2) as total'),
            ])
            ->get();

        return $items->map(fn ($item) => (array) $item)->toArray();
    }

    /**
     * Get the count of distributors referred by a user before a specific date.
     */
    public function getReferredDistributorsCount(int $userId, string $beforeDate): int
    {
        return DB::table('users as u')
            ->join('user_category as uc', function ($join): void {
                $join->on('u.id', '=', 'uc.user_id')
                    ->where('uc.category_id', '=', self::CATEGORY_DISTRIBUTOR);
            })
            ->where('u.referred_by', '=', $userId)
            ->where('u.enrolled_date', '<=', $beforeDate)
            ->count();
    }

    /**
     * Get the commission percentage based on referred distributors count.
     */
    public function getCommissionPercentage(int $referredCount): int
    {
        foreach (self::COMMISSION_TIERS as $tier) {
            if ($referredCount >= $tier['min'] && $referredCount <= $tier['max']) {
                return $tier['percentage'];
            }
        }

        return 0;
    }

    /**
     * Build the base query for commission report.
     */
    private function buildBaseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('orders as o')
            ->join('users as purchaser', 'o.purchaser_id', '=', 'purchaser.id')
            ->leftJoin('users as referrer', 'purchaser.referred_by', '=', 'referrer.id')
            ->leftJoin('user_category as uc_referrer', function ($join): void {
                $join->on('referrer.id', '=', 'uc_referrer.user_id')
                    ->where('uc_referrer.category_id', '=', self::CATEGORY_DISTRIBUTOR);
            })
            ->leftJoin('user_category as uc_purchaser', function ($join): void {
                $join->on('purchaser.id', '=', 'uc_purchaser.user_id')
                    ->where('uc_purchaser.category_id', '=', self::CATEGORY_CUSTOMER);
            })
            ->select([
                'o.id as order_id',
                'o.invoice_number',
                'o.order_date',
                'purchaser.id as purchaser_id',
                DB::raw("CONCAT(purchaser.first_name, ' ', purchaser.last_name) as purchaser_name"),
                'referrer.id as referrer_id',
                DB::raw("CASE WHEN uc_referrer.user_id IS NOT NULL THEN CONCAT(referrer.first_name, ' ', referrer.last_name) ELSE NULL END as distributor_name"),
                DB::raw('CASE WHEN uc_referrer.user_id IS NOT NULL THEN referrer.id ELSE NULL END as distributor_id'),
                DB::raw('CASE WHEN uc_purchaser.user_id IS NOT NULL THEN 1 ELSE 0 END as is_customer'),
            ]);
    }

    /**
     * Apply filters to the query.
     *
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(\Illuminate\Database\Query\Builder $query, array $filters): void
    {
        if (! empty($filters['distributor'])) {
            $distributorSearch = $filters['distributor'];
            $query->where(function ($q) use ($distributorSearch): void {
                $q->where('referrer.id', '=', $distributorSearch)
                    ->orWhere('referrer.first_name', 'LIKE', "%{$distributorSearch}%")
                    ->orWhere('referrer.last_name', 'LIKE', "%{$distributorSearch}%");
            })->whereNotNull('uc_referrer.user_id');
        }

        if (! empty($filters['date_from'])) {
            $query->where('o.order_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('o.order_date', '<=', $filters['date_to']);
        }

        if (! empty($filters['invoice'])) {
            $query->where('o.invoice_number', 'LIKE', "%{$filters['invoice']}%");
        }
    }

    /**
     * Process orders and calculate commissions.
     *
     * @return array<int, array<string, mixed>>
     */
    private function processOrders(\Illuminate\Support\Collection $orders): array
    {
        $results = [];

        foreach ($orders as $order) {
            $orderTotal = $this->calculateOrderTotal((int) $order->order_id);

            $referredDistributors = 0;
            $percentage = 0;
            $commission = 0.0;

            if ($order->distributor_id && $order->is_customer) {
                $referredDistributors = $this->getReferredDistributorsCount(
                    (int) $order->distributor_id,
                    $order->order_date
                );
                $percentage = $this->getCommissionPercentage($referredDistributors);
                $commission = round($orderTotal * ($percentage / 100), 2);
            }

            $results[] = [
                'order_id' => (int) $order->order_id,
                'invoice' => $order->invoice_number,
                'purchaser' => $order->purchaser_name,
                'purchaser_id' => (int) $order->purchaser_id,
                'distributor' => $order->distributor_name,
                'distributor_id' => $order->distributor_id ? (int) $order->distributor_id : null,
                'referred_distributors' => $referredDistributors,
                'order_date' => $order->order_date,
                'order_total' => round($orderTotal, 2),
                'percentage' => $percentage,
                'commission' => $commission,
            ];
        }

        return $results;
    }

    /**
     * Calculate the total amount of an order.
     */
    private function calculateOrderTotal(int $orderId): float
    {
        $result = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('oi.order_id', '=', $orderId)
            ->select(DB::raw('SUM(p.price * oi.quantity) as total'))
            ->first();

        return (float) ($result->total ?? 0);
    }
}

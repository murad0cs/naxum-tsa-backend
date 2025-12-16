<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\TopDistributorsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TopDistributorsRepository implements TopDistributorsRepositoryInterface
{
    /**
     * Category ID for Distributor users.
     */
    private const CATEGORY_DISTRIBUTOR = 1;

    /**
     * Get top distributors by total sales.
     * Total sales equals the sum of orders from customers and distributors they referred.
     * Returns distributors with rank less than or equal to limit (includes ties at the boundary).
     *
     * @param  int  $limit  Maximum number of distributors to return
     * @param  int  $perPage  Items per page for pagination
     * @return array<int, array<string, mixed>>
     */
    public function getTopDistributors(int $limit = 200, int $perPage = 10): array
    {
        $distributors = $this->fetchDistributorsWithSales();

        return $this->assignRanks($distributors, $limit);
    }

    /**
     * Get paginated top distributors.
     *
     * @param  int  $limit  Maximum number of distributors
     * @param  int  $perPage  Items per page
     * @param  int  $page  Current page number
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getTopDistributorsPaginated(int $limit = 200, int $perPage = 10, int $page = 1): array
    {
        $allDistributors = $this->getTopDistributors($limit, $perPage);

        $offset = ($page - 1) * $perPage;
        $total = count($allDistributors);

        $paginatedData = array_slice($allDistributors, $offset, $perPage);

        return [
            'data' => $paginatedData,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Fetch all distributors with their total sales.
     */
    private function fetchDistributorsWithSales(): \Illuminate\Support\Collection
    {
        return DB::table('users as distributor')
            ->join('user_category as uc_dist', function ($join): void {
                $join->on('distributor.id', '=', 'uc_dist.user_id')
                    ->where('uc_dist.category_id', '=', self::CATEGORY_DISTRIBUTOR);
            })
            ->join('users as referred', 'referred.referred_by', '=', 'distributor.id')
            ->join('orders as o', 'o.purchaser_id', '=', 'referred.id')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'p.id', '=', 'oi.product_id')
            ->select([
                'distributor.id',
                DB::raw("CONCAT(distributor.first_name, ' ', distributor.last_name) as distributor_name"),
                DB::raw('SUM(p.price * oi.quantity) as total_sales'),
            ])
            ->groupBy('distributor.id', 'distributor.first_name', 'distributor.last_name')
            ->orderByDesc('total_sales')
            ->get();
    }

    /**
     * Assign ranks to distributors using DENSE_RANK logic.
     * Distributors with the same sales amount receive the same rank.
     * The next different sales amount gets the next consecutive rank (no gaps).
     *
     * @return array<int, array<string, mixed>>
     */
    private function assignRanks(\Illuminate\Support\Collection $distributors, int $limit): array
    {
        $results = [];
        $rank = 0;
        $previousSales = null;

        foreach ($distributors as $distributor) {
            $currentSales = (float) $distributor->total_sales;

            // DENSE_RANK: increment rank only when sales value changes
            if ($previousSales === null || $currentSales !== $previousSales) {
                $rank++;
            }

            // Stop if rank exceeds limit
            if ($rank > $limit) {
                break;
            }

            $results[] = [
                'rank' => $rank,
                'distributor_id' => (int) $distributor->id,
                'distributor_name' => $distributor->distributor_name,
                'total_sales' => round($currentSales, 2),
            ];

            $previousSales = $currentSales;
        }

        return $results;
    }
}

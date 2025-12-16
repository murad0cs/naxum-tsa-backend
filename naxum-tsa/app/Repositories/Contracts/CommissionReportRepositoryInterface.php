<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface CommissionReportRepositoryInterface
{
    /**
     * Get the commission report with filters and pagination.
     *
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getCommissionReport(array $filters = [], int $perPage = 10): array;

    /**
     * Get order items for a specific order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrderItems(int $orderId): array;

    /**
     * Get the count of distributors referred by a user before a specific date.
     */
    public function getReferredDistributorsCount(int $userId, string $beforeDate): int;

    /**
     * Get the commission percentage based on referred distributors count.
     */
    public function getCommissionPercentage(int $referredCount): int;
}

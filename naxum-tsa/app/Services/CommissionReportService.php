<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CommissionReportRepositoryInterface;

class CommissionReportService
{
    public function __construct(
        private readonly CommissionReportRepositoryInterface $repository
    ) {}

    /**
     * Get the commission report with optional filters.
     *
     * @param  array<string, mixed>  $filters
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getReport(array $filters = [], int $perPage = 10): array
    {
        return $this->repository->getCommissionReport($filters, $perPage);
    }

    /**
     * Get order items for a specific order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrderItems(int $orderId): array
    {
        return $this->repository->getOrderItems($orderId);
    }

    /**
     * Get referred distributors count for a user before a date.
     */
    public function getReferredDistributorsCount(int $userId, string $beforeDate): int
    {
        return $this->repository->getReferredDistributorsCount($userId, $beforeDate);
    }

    /**
     * Get commission percentage based on referred distributors count.
     */
    public function getCommissionPercentage(int $referredCount): int
    {
        return $this->repository->getCommissionPercentage($referredCount);
    }
}

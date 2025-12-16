<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface TopDistributorsRepositoryInterface
{
    /**
     * Get top distributors by total sales.
     *
     * @param  int  $limit  Maximum number of distributors to return
     * @param  int  $perPage  Items per page for pagination
     * @return array<int, array<string, mixed>>
     */
    public function getTopDistributors(int $limit = 200, int $perPage = 10): array;

    /**
     * Get paginated top distributors.
     *
     * @param  int  $limit  Maximum number of distributors
     * @param  int  $perPage  Items per page
     * @param  int  $page  Current page number
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getTopDistributorsPaginated(int $limit = 200, int $perPage = 10, int $page = 1): array;
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\TopDistributorsRepositoryInterface;

class TopDistributorsService
{
    public function __construct(
        private readonly TopDistributorsRepositoryInterface $repository
    ) {}

    /**
     * Get top distributors with ranking.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTopDistributors(int $limit = 200): array
    {
        return $this->repository->getTopDistributors($limit);
    }

    /**
     * Get paginated top distributors.
     *
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int>}
     */
    public function getTopDistributorsPaginated(int $limit = 200, int $perPage = 10, int $page = 1): array
    {
        return $this->repository->getTopDistributorsPaginated($limit, $perPage, $page);
    }
}

<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class TopDistributorDTO
{
    public function __construct(
        public int $rank,
        public int $distributorId,
        public string $distributorName,
        public float $totalSales,
    ) {}

    /**
     * Create DTO from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rank: (int) $data['rank'],
            distributorId: (int) $data['distributor_id'],
            distributorName: (string) $data['distributor_name'],
            totalSales: (float) $data['total_sales'],
        );
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return [
            'rank' => $this->rank,
            'distributor_id' => $this->distributorId,
            'distributor_name' => $this->distributorName,
            'total_sales' => $this->totalSales,
        ];
    }
}




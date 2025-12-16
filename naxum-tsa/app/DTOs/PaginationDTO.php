<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class PaginationDTO
{
    public function __construct(
        public int $currentPage,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}

    /**
     * Create pagination DTO from parameters.
     */
    public static function create(int $currentPage, int $perPage, int $total): self
    {
        return new self(
            currentPage: $currentPage,
            perPage: $perPage,
            total: $total,
            lastPage: (int) ceil($total / $perPage),
        );
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'last_page' => $this->lastPage,
        ];
    }
}




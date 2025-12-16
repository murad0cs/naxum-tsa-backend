<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class OrderItemDTO
{
    public function __construct(
        public string $sku,
        public string $productName,
        public float $price,
        public int $quantity,
        public float $total,
    ) {}

    /**
     * Create DTO from database object.
     */
    public static function fromObject(object $data): self
    {
        return new self(
            sku: (string) $data->sku,
            productName: (string) $data->product_name,
            price: (float) $data->price,
            quantity: (int) $data->quantity,
            total: (float) $data->total,
        );
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'product_name' => $this->productName,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
        ];
    }
}




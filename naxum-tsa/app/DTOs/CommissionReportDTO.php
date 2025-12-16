<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CommissionReportDTO
{
    public function __construct(
        public int $orderId,
        public string $invoice,
        public string $purchaser,
        public int $purchaserId,
        public ?string $distributor,
        public ?int $distributorId,
        public int $referredDistributors,
        public string $orderDate,
        public float $orderTotal,
        public int $percentage,
        public float $commission,
    ) {}

    /**
     * Create DTO from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            orderId: (int) $data['order_id'],
            invoice: (string) $data['invoice'],
            purchaser: (string) $data['purchaser'],
            purchaserId: (int) $data['purchaser_id'],
            distributor: $data['distributor'],
            distributorId: $data['distributor_id'] ? (int) $data['distributor_id'] : null,
            referredDistributors: (int) $data['referred_distributors'],
            orderDate: (string) $data['order_date'],
            orderTotal: (float) $data['order_total'],
            percentage: (int) $data['percentage'],
            commission: (float) $data['commission'],
        );
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'invoice' => $this->invoice,
            'purchaser' => $this->purchaser,
            'purchaser_id' => $this->purchaserId,
            'distributor' => $this->distributor,
            'distributor_id' => $this->distributorId,
            'referred_distributors' => $this->referredDistributors,
            'order_date' => $this->orderDate,
            'order_total' => $this->orderTotal,
            'percentage' => $this->percentage,
            'commission' => $this->commission,
        ];
    }
}




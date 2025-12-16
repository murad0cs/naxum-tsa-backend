<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->resource['order_id'],
            'invoice' => $this->resource['invoice'],
            'purchaser' => $this->resource['purchaser'],
            'purchaser_id' => $this->resource['purchaser_id'],
            'distributor' => $this->resource['distributor'],
            'distributor_id' => $this->resource['distributor_id'],
            'referred_distributors' => $this->resource['referred_distributors'],
            'order_date' => $this->resource['order_date'],
            'order_total' => number_format((float) $this->resource['order_total'], 2, '.', ''),
            'percentage' => $this->resource['percentage'],
            'commission' => number_format((float) $this->resource['commission'], 2, '.', ''),
        ];
    }
}

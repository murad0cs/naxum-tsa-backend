<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sku' => $this->resource['sku'],
            'product_name' => $this->resource['product_name'],
            'price' => number_format((float) $this->resource['price'], 2, '.', ''),
            'quantity' => $this->resource['quantity'],
            'total' => number_format((float) $this->resource['total'], 2, '.', ''),
        ];
    }
}

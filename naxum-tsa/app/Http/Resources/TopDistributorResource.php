<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopDistributorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rank' => $this->resource['rank'],
            'distributor_id' => $this->resource['distributor_id'],
            'distributor_name' => $this->resource['distributor_name'],
            'total_sales' => number_format((float) $this->resource['total_sales'], 2, '.', ''),
        ];
    }
}

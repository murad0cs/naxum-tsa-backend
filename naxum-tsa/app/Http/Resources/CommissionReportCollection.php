<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommissionReportCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = CommissionReportResource::class;

    /**
     * Additional pagination data.
     *
     * @var array<string, mixed>
     */
    private array $pagination = [];

    /**
     * Set pagination data.
     *
     * @param  array<string, mixed>  $pagination
     */
    public function setPagination(array $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => $this->pagination,
        ];
    }

    /**
     * Customize the response.
     *
     * @param  \Illuminate\Http\JsonResponse  $response
     */
    public function withResponse(Request $request, $response): void
    {
        $data = $response->getData(true);
        $data['success'] = true;
        $response->setData($data);
    }
}




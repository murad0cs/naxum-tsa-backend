<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'distributor' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'invoice' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'date_from.date_format' => 'The date from must be in Y-m-d format.',
            'date_to.date_format' => 'The date to must be in Y-m-d format.',
            'date_to.after_or_equal' => 'The date to must be after or equal to date from.',
            'per_page.max' => 'The per page value cannot exceed 100.',
        ];
    }

    /**
     * Get the validated filters array.
     */
    public function getFilters(): array
    {
        return [
            'distributor' => $this->input('distributor'),
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
            'invoice' => $this->input('invoice'),
            'page' => $this->input('page', 1),
        ];
    }

    /**
     * Get the per page value.
     */
    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 10);
    }
}




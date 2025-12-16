<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopDistributorsRequest extends FormRequest
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
        // Allow per_page up to 250 to accommodate ties at rank boundaries
        // The actual limit is 200 ranks, but with ties there may be more records
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:250'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'per_page.max' => 'The per page value cannot exceed 250.',
        ];
    }

    /**
     * Get the per page value.
     */
    public function getPerPage(): int
    {
        return (int) $this->input('per_page', 10);
    }

    /**
     * Get the page number.
     */
    public function getPage(): int
    {
        return (int) $this->input('page', 1);
    }
}

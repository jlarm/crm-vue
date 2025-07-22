<?php

namespace App\Domains\Dealership\Http\Requests;

use App\Enums\DevStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDealershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:500'],
            'city' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'size:2'],
            'zip_code' => ['sometimes', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'phone' => ['sometimes', 'string', 'regex:/^[\+]?[1-9][\d\s\-\(\)]{7,15}$/'],
            'email' => ['sometimes', 'email', 'max:255'],
            'current_solution_name' => ['nullable', 'string', 'max:255'],
            'current_solution_use' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'type' => ['nullable', 'string', Rule::in(['Automotive', 'RV', 'Motorsports', 'Maritime'])],
            'in_development' => ['sometimes', 'boolean'],
            'dev_status' => ['nullable', Rule::enum(DevStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'zip_code.regex' => 'The zip code must be in format 12345 or 12345-6789',
            'phone.regex' => 'The phone number format is invalid',
            'state.size' => 'The state must be a 2-letter abbreviation',
            'rating.between' => 'Rating must be between 1 and 5',
        ];
    }
}

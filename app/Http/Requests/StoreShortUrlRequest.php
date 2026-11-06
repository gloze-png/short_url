<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShortUrlRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048'],
            'custom_alias' => [
                'nullable',
                'alpha_dash',
                'min:4',
                'max:30',
                'unique:short_urls,short_code',
            ],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'custom_alias.unique' => 'That alias is already taken. Please choose another.',
            'expires_at.after'    => 'The expiration date must be in the future.',
        ];
    }
}

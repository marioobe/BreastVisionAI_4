<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAiModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'version' => 'sometimes|string|max:50',
            'metrics' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nama model harus berupa string.',
            'name.max' => 'Nama model maksimal 255 karakter.',
            'version.string' => 'Versi harus berupa string.',
            'version.max' => 'Versi maksimal 50 karakter.',
            'metrics.json' => 'Metrics harus berupa JSON string.',
        ];
    }
}

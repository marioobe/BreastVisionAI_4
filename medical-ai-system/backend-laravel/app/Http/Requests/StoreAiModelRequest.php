<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAiModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'model_file' => 'required|file|mimes:keras,h5|max:204800',
            'metrics' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama model wajib diisi.',
            'version.required' => 'Versi model wajib diisi.',
            'model_file.required' => 'File model wajib diupload.',
            'model_file.mimes' => 'File model harus format .keras atau .h5.',
            'model_file.max' => 'Ukuran file model maksimal 200MB.',
            'metrics.json' => 'Metrics harus berupa JSON string.',
        ];
    }
}

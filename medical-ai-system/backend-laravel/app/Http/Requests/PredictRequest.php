<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'date_of_birth' => 'required|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.max' => 'NIK maksimal 20 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus L atau P.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'image.required' => 'Gambar wajib diupload.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, jpg, atau png.',
            'image.max' => 'Ukuran gambar maksimal 10MB.',
        ];
    }
}

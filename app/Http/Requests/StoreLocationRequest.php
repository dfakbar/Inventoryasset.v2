<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:3', 'max:150'],
            'department'  => ['nullable', 'string', 'max:100'],
            // Slug opsional; jika tidak diisi akan digenerate otomatis dari nama
            'slug'        => [
                'nullable',
                'string',
                'max:200',
                'alpha_dash',
                Rule::unique('locations', 'slug'),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama lokasi wajib diisi.',
            'name.min'        => 'Nama lokasi minimal :min karakter.',
            'slug.alpha_dash' => 'Slug hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'slug.unique'     => 'Slug sudah digunakan oleh lokasi lain.',
        ];
    }
}

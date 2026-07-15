<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'asset_id'    => ['nullable', 'integer', 'exists:assets,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'subject'     => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'priority'    => ['required', new Enum(TicketPriority::class)],
            'source'      => ['nullable', new Enum(TicketSource::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori tiket wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'subject.required'     => 'Judul tiket wajib diisi.',
            'subject.min'          => 'Judul tiket minimal :min karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min'      => 'Deskripsi minimal :min karakter.',
            'priority.required'    => 'Prioritas wajib dipilih.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('source')) {
            $this->merge(['source' => 'Web']);
        }
    }
}

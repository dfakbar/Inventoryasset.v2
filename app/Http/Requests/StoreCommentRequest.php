<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body'        => ['required', 'string', 'min:1', 'max:10000'],
            'is_internal' => ['sometimes', 'boolean'],
            'attachment'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Komentar tidak boleh kosong.',
            'attachment.max' => 'Ukuran file tidak boleh melebihi 5 MB.',
        ];
    }
}

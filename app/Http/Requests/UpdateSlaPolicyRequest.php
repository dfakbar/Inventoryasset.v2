<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSlaPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priority'         => ['sometimes', new Enum(TicketPriority::class)],
            'respond_hours'    => ['sometimes', 'integer', 'min:1', 'max:168'],
            'resolve_hours'    => ['sometimes', 'integer', 'min:1', 'max:720'],
            'is_active'        => ['sometimes', 'boolean'],
            'escalate_minutes' => ['sometimes', 'integer', 'min:0', 'max:1440'],
        ];
    }
}

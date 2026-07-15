<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSlaPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priority'        => ['required', new Enum(TicketPriority::class), 'unique:sla_policies,priority'],
            'respond_hours'   => ['required', 'integer', 'min:1', 'max:168'],
            'resolve_hours'   => ['required', 'integer', 'min:1', 'max:720'],
            'is_active'       => ['sometimes', 'boolean'],
            'escalate_minutes' => ['sometimes', 'integer', 'min:0', 'max:1440'],
        ];
    }
}

<?php

namespace App\Enums;

enum TicketSource: string
{
    case Web      = 'Web';
    case Email    = 'Email';
    case WhatsApp = 'WhatsApp';
    case Phone    = 'Phone';

    public function label(): string
    {
        return match($this) {
            self::Web      => 'Website',
            self::Email    => 'Email',
            self::WhatsApp => 'WhatsApp',
            self::Phone    => 'Telepon',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

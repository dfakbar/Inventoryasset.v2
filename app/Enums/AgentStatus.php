<?php

namespace App\Enums;

enum AgentStatus: string
{
    case Available = 'Available';
    case Busy      = 'Busy';
    case Away      = 'Away';
    case Offline   = 'Offline';

    public function label(): string
    {
        return match($this) {
            self::Available => 'Tersedia',
            self::Busy      => 'Sibuk',
            self::Away      => 'Keluar',
            self::Offline   => 'Offline',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Available => 'badge bg-success',
            self::Busy      => 'badge bg-warning text-dark',
            self::Away      => 'badge bg-secondary',
            self::Offline   => 'badge bg-dark',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function availableForAssignment(): array
    {
        return [self::Available, self::Busy];
    }
}

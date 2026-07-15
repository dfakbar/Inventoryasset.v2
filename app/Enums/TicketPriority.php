<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Urgent = 'Urgent';
    case High   = 'High';
    case Medium = 'Medium';
    case Low    = 'Low';

    public function label(): string
    {
        return match($this) {
            self::Urgent => 'Urgent',
            self::High   => 'Tinggi',
            self::Medium => 'Sedang',
            self::Low    => 'Rendah',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Urgent => 'badge bg-danger',
            self::High   => 'badge bg-warning text-dark',
            self::Medium => 'badge bg-primary',
            self::Low    => 'badge bg-success',
        };
    }

    public function slaRespondHours(): int
    {
        return match($this) {
            self::Urgent => 1,
            self::High   => 2,
            self::Medium => 4,
            self::Low    => 8,
        };
    }

    public function slaResolveHours(): int
    {
        return match($this) {
            self::Urgent => 4,
            self::High   => 8,
            self::Medium => 24,
            self::Low    => 40,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

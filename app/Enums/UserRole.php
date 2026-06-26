<?php

namespace App\Enums;

/**
 * Merepresentasikan peran (role) pengguna dalam sistem.
 * PHP 8.1 Backed Enum untuk type-safety penuh.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Staff = 'staff';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::Staff => 'Staff',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Admin => 'badge bg-danger',
            self::Staff => 'badge bg-primary',
        };
    }

    /**
     * Kembalikan semua nilai string (berguna untuk validasi rule).
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace App\Enums;

/**
 * Merepresentasikan seluruh status siklus hidup (lifecycle) sebuah aset.
 * Menggunakan PHP 8.1 Backed Enum untuk type-safety penuh.
 */
enum AssetStatus: string
{
    case InUse       = 'In Use';
    case Spare       = 'Spare';
    case Service     = 'Service';
    case Broken      = 'Broken';
    case Disposal    = 'Disposal';
    case BrokenCheck = 'Broken-Check';

    /**
     * Label yang ditampilkan ke pengguna.
     */
    public function label(): string
    {
        return match($this) {
            self::InUse       => 'Sedang Digunakan',
            self::Spare       => 'Cadangan',
            self::Service     => 'Dalam Servis',
            self::Broken      => 'Rusak',
            self::Disposal    => 'Disposal',
            self::BrokenCheck => 'Rusak - Cek Ulang',
        };
    }

    /**
     * Kelas Bootstrap 5 badge yang sesuai dengan setiap status.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::InUse       => 'badge bg-success',
            self::Spare       => 'badge bg-info text-dark',
            self::Service     => 'badge bg-warning text-dark',
            self::Broken      => 'badge bg-danger',
            self::Disposal    => 'badge bg-secondary',
            self::BrokenCheck => 'badge bg-dark',
        };
    }

    /**
     * Ikon Bootstrap Icons yang sesuai dengan setiap status.
     */
    public function icon(): string
    {
        return match($this) {
            self::InUse       => 'bi-check-circle-fill',
            self::Spare       => 'bi-archive-fill',
            self::Service     => 'bi-tools',
            self::Broken      => 'bi-x-circle-fill',
            self::Disposal    => 'bi-trash3-fill',
            self::BrokenCheck => 'bi-exclamation-triangle-fill',
        };
    }

    /**
     * Mengembalikan array semua nilai string (berguna untuk validasi rules).
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

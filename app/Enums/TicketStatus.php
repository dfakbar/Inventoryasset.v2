<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Draft      = 'Draft';
    case New        = 'New';
    case Assigned   = 'Assigned';
    case InProgress = 'In Progress';
    case Pending    = 'Pending';
    case Resolved   = 'Resolved';
    case Closed     = 'Closed';
    case Rejected   = 'Rejected';
    case Reopen     = 'Reopen';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Konsep',
            self::New        => 'Baru',
            self::Assigned   => 'Ditugaskan',
            self::InProgress => 'Diproses',
            self::Pending    => 'Ditunda',
            self::Resolved   => 'Terselesaikan',
            self::Closed     => 'Ditutup',
            self::Rejected   => 'Ditolak',
            self::Reopen     => 'Dibuka Kembali',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft      => 'badge bg-secondary',
            self::New        => 'badge bg-primary',
            self::Assigned   => 'badge bg-info text-dark',
            self::InProgress => 'badge bg-warning text-dark',
            self::Pending    => 'badge bg-dark',
            self::Resolved   => 'badge bg-success',
            self::Closed     => 'badge bg-light text-dark border',
            self::Rejected   => 'badge bg-danger',
            self::Reopen     => 'badge bg-danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Draft      => 'bi-file-earmark',
            self::New        => 'bi-plus-circle-fill',
            self::Assigned   => 'bi-person-check-fill',
            self::InProgress => 'bi-arrow-repeat',
            self::Pending    => 'bi-pause-circle-fill',
            self::Resolved   => 'bi-check2-circle',
            self::Closed     => 'bi-lock-fill',
            self::Rejected   => 'bi-x-octagon-fill',
            self::Reopen     => 'bi-arrow-counterclockwise',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function activeStatuses(): array
    {
        return [self::New, self::Assigned, self::InProgress, self::Pending, self::Reopen];
    }

    public static function resolutiveStatuses(): array
    {
        return [self::Resolved, self::Closed, self::Rejected];
    }
}

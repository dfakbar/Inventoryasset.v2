<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketCodeGenerator
{
    private const PREFIX    = 'TKT';
    private const SEQ_PAD   = 5;

    public function generate(?Ticket $ticket = null): string
    {
        $date = now();
        $year  = $date->format('y');
        $month = $date->format('m');

        $codePrefix = self::PREFIX . $year . $month;

        $lastCode = Ticket::withTrashed()
            ->where('ticket_number', 'like', $codePrefix . '%')
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('ticket_number');

        $nextSequence = $this->resolveNextSequence($lastCode, strlen($codePrefix));

        return $codePrefix . str_pad($nextSequence, self::SEQ_PAD, '0', STR_PAD_LEFT);
    }

    private function resolveNextSequence(?string $lastCode, int $prefixLength): int
    {
        if ($lastCode === null) {
            return 1;
        }

        $lastSequence = (int) substr($lastCode, $prefixLength);
        return max($lastSequence + 1, 1);
    }
}

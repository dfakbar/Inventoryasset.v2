<?php

namespace App\Services;

use App\Enums\TicketPriority;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use Carbon\Carbon;

class SlaCalculator
{
    private const WORK_START = 8;
    private const WORK_END   = 17;
    private const WORK_HOURS = 9;

    public function calculateForPriority(TicketPriority $priority): array
    {
        $policy = SlaPolicy::where('priority', $priority->value)->where('is_active', true)->first();

        $respondHours  = $policy?->respond_hours ?? $priority->slaRespondHours();
        $resolveHours  = $policy?->resolve_hours ?? $priority->slaResolveHours();

        $now = now();

        return [
            'respond_at'  => $this->addBusinessHours($now, $respondHours),
            'resolve_at'  => $this->addBusinessHours($now, $resolveHours),
        ];
    }

    public function recalculateForTicket(Ticket $ticket): void
    {
        $priority = $ticket->priority;
        $deadlines = $this->calculateForPriority($priority);

        $totalPaused = $ticket->sla_paused_total_minutes ?? 0;

        $ticket->sla_respond_at = $deadlines['respond_at']?->addMinutes($totalPaused);
        $ticket->sla_resolve_at = $deadlines['resolve_at']?->addMinutes($totalPaused);
        $ticket->save();
    }

    public function addBusinessHours(Carbon $start, int $hours): Carbon
    {
        $current = $start->copy();
        $remainingMinutes = $hours * 60;

        while ($remainingMinutes > 0) {
            if ($current->isWeekend()) {
                $current->startOfDay()->addDay()->setHour(self::WORK_START);
                continue;
            }

            $currentHour = $current->hour + ($current->minute / 60);
            $workEndHour = self::WORK_END;

            if ($currentHour < self::WORK_START) {
                $current->setHour(self::WORK_START)->setMinute(0)->setSecond(0);
                $currentHour = self::WORK_START;
            }

            if ($currentHour >= $workEndHour) {
                $current->startOfDay()->addDay()->setHour(self::WORK_START);
                continue;
            }

            $availableToday = ($workEndHour - max($currentHour, self::WORK_START)) * 60;
            $usable = min($availableToday, $remainingMinutes);

            $current->addMinutes((int) $usable);
            $remainingMinutes -= $usable;
        }

        return $current;
    }

    public function isSlaBreached(Ticket $ticket): bool
    {
        if (!$ticket->sla_resolve_at) {
            return false;
        }

        if (in_array($ticket->status->value, ['Closed', 'Resolved', 'Rejected'])) {
            return false;
        }

        return now()->greaterThan($ticket->sla_resolve_at);
    }

    public function getSlaProgressPercent(Ticket $ticket): int
    {
        if (!$ticket->sla_resolve_at || !$ticket->created_at) {
            return 0;
        }

        if (in_array($ticket->status->value, ['Closed', 'Resolved', 'Rejected'])) {
            return 100;
        }

        $totalDuration = $ticket->created_at->diffInMinutes($ticket->sla_resolve_at);
        if ($totalDuration <= 0) {
            return 100;
        }

        $elapsed = $ticket->created_at->diffInMinutes(now());
        $elapsed -= ($ticket->sla_paused_total_minutes ?? 0);

        return min((int) round(($elapsed / $totalDuration) * 100), 100);
    }
}

<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Services\SlaCalculator;
use App\Services\TicketCodeGenerator;
use App\Services\TicketLogService;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    public function __construct(
        private readonly TicketCodeGenerator $codeGenerator,
        private readonly SlaCalculator $slaCalculator,
    ) {}

    public function creating(Ticket $ticket): void
    {
        if (empty($ticket->ticket_number)) {
            $ticket->ticket_number = $this->codeGenerator->generate($ticket);
        }

        $priority = $ticket->priority;
        $deadlines = $this->slaCalculator->calculateForPriority($priority);

        $ticket->sla_respond_at = $deadlines['respond_at'];
        $ticket->sla_resolve_at = $deadlines['resolve_at'];
    }

    public function created(Ticket $ticket): void
    {
        TicketLogService::log(
            $ticket,
            $ticket->requester_id,
            'created',
            'status',
            null,
            'New',
            'Tiket baru dibuat'
        );
    }

    public function updating(Ticket $ticket): void
    {
        // Jika prioritas berubah, hitung ulang SLA
        if ($ticket->isDirty('priority') && $ticket->getOriginal('priority') !== null) {
            $oldPriority = $ticket->getOriginal('priority');
            Log::info("TicketObserver: Prioritas tiket {$ticket->ticket_number} berubah dari {$oldPriority} ke {$ticket->priority->value}");
        }
    }

    public function updated(Ticket $ticket): void
    {
        $changes = [];

        if ($ticket->wasChanged('status')) {
            $old = $ticket->getOriginal('status');
            $new = $ticket->status->value;

            TicketLogService::statusChange($ticket, $old, $new);

            if ($new === 'Assigned' && !$ticket->first_response_at) {
                $ticket->first_response_at = now();
                $ticket->saveQuietly();
            }

            if ($new === 'Resolved' && !$ticket->resolved_at) {
                $ticket->resolved_at = now();
                $ticket->saveQuietly();
            }

            if ($new === 'Closed' && !$ticket->closed_at) {
                $ticket->closed_at = now();
                $ticket->saveQuietly();
            }

            $changes[] = "status: {$old} → {$new}";
        }

        if ($ticket->wasChanged('agent_id')) {
            $old = $ticket->getOriginal('agent_id');
            $new = $ticket->agent_id;

            TicketLogService::assignment($ticket, $old, $new);

            $changes[] = "agent: {$old} → {$new}";
        }

        if ($ticket->wasChanged('priority')) {
            $old = $ticket->getOriginal('priority');
            $new = $ticket->priority->value;

            TicketLogService::log($ticket, null, 'priority_changed', 'priority', $old, $new);

            $this->slaCalculator->recalculateForTicket($ticket);

            $changes[] = "priority: {$old} → {$new}";
        }

        if (!empty($changes)) {
            Log::info("TicketObserver: Tiket {$ticket->ticket_number} diupdate: " . implode(', ', $changes));
        }
    }
}

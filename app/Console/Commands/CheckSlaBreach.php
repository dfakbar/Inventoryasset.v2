<?php

namespace App\Console\Commands;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\User;
use App\Services\SlaCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSlaBreach extends Command
{
    protected $signature = 'sd:check-sla';
    protected $description = 'Periksa tiket yang melanggar SLA dan lakukan eskalasi';

    public function handle(SlaCalculator $slaCalculator): int
    {
        $this->info('Memeriksa SLA breach...');

        $activeStatuses = TicketStatus::activeStatuses();
        $activeStatusValues = array_map(fn($s) => $s->value, $activeStatuses);

        $breachedTickets = Ticket::whereNotNull('sla_resolve_at')
            ->where('sla_resolve_at', '<', now())
            ->whereIn('status', $activeStatusValues)
            ->get();

        if ($breachedTickets->isEmpty()) {
            $this->info('Tidak ada tiket yang melanggar SLA.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$breachedTickets->count()} tiket melanggar SLA.");

        foreach ($breachedTickets as $ticket) {
            $this->processBreach($ticket);
        }

        return self::SUCCESS;
    }

    private function processBreach(Ticket $ticket): void
    {
        $alreadyEscalated = TicketEscalation::where('ticket_id', $ticket->id)
            ->where('level', 'Level1')
            ->exists();

        if ($alreadyEscalated) {
            return;
        }

        $admin = User::role('admin')->first();

        if (!$admin) {
            Log::warning("CheckSlaBreach: Tidak ada admin untuk eskalasi tiket {$ticket->ticket_number}");
            return;
        }

        TicketEscalation::create([
            'ticket_id'    => $ticket->id,
            'escalated_to' => $admin->id,
            'escalated_by' => $admin->id,
            'reason'       => 'SLA Breach - tiket melewati batas waktu resolusi',
            'level'        => 'Level1',
        ]);

        Log::info("CheckSlaBreach: Tiket {$ticket->ticket_number} dieskalasi ke {$admin->name}");
        $this->info("  -> Tiket {$ticket->ticket_number} dieskalasi ke Admin.");
    }
}

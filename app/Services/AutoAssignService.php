<?php

namespace App\Services;

use App\Enums\AgentStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAssignService
{
    public function assign(Ticket $ticket): bool
    {
        if ($ticket->agent_id) {
            return false;
        }

        DB::beginTransaction();
        try {
            $agent = $this->findBestAgent($ticket);

            if (!$agent) {
                Log::info("AutoAssign: Tidak ada agent tersedia untuk tiket #{$ticket->ticket_number}");
                DB::commit();
                return false;
            }

            $ticket->agent_id = $agent->id;
            $ticket->status = 'Assigned';
            $ticket->save();

            TicketLogService::log(
                $ticket,
                auth()->id() ?? $agent->id,
                'assigned',
                'agent_id',
                null,
                (string) $agent->id,
                'Auto-assignment ke agent dengan workload terendah'
            );

            DB::commit();

            Log::info("AutoAssign: Tiket #{$ticket->ticket_number} di-assign ke {$agent->name} (ID: {$agent->id})");

            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("AutoAssign gagal: {$e->getMessage()}", [
                'ticket_id' => $ticket->id,
            ]);
            return false;
        }
    }

    private function findBestAgent(Ticket $ticket): ?User
    {
        $categoryId = $ticket->category_id;

        $specialists = User::role('agent')
            ->whereHas('agentStatus', function ($q) {
                $q->whereIn('status', AgentStatus::availableForAssignment());
            })
            ->whereHas('specializations', function ($q) use ($categoryId) {
                $q->where('ticket_category_id', $categoryId);
            })
            ->withCount(['assignedTickets' => function ($q) {
                $q->whereIn('status', ['Assigned', 'In Progress']);
            }])
            ->orderBy('assigned_tickets_count')
            ->orderBy('updated_at')
            ->get();

        if ($specialists->isNotEmpty()) {
            return $specialists->first();
        }

        return User::role('agent')
            ->whereHas('agentStatus', function ($q) {
                $q->whereIn('status', AgentStatus::availableForAssignment());
            })
            ->withCount(['assignedTickets' => function ($q) {
                $q->whereIn('status', ['Assigned', 'In Progress']);
            }])
            ->orderBy('assigned_tickets_count')
            ->orderBy('updated_at')
            ->first();
    }
}

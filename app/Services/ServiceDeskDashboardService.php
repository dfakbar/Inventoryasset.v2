<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ServiceDeskDashboardService
{
    public function getStats(): array
    {
        return [
            'total_open'      => Ticket::whereIn('status', TicketStatus::activeStatuses())->count(),
            'total_today'     => Ticket::whereDate('created_at', today())->count(),
            'unassigned'      => Ticket::whereNull('agent_id')->where('status', 'New')->count(),
            'sla_breached'    => Ticket::whereNotNull('sla_resolve_at')
                ->where('sla_resolve_at', '<', now())
                ->whereNotIn('status', ['Closed', 'Resolved', 'Rejected'])
                ->count(),
            'urgent_open'     => Ticket::where('priority', 'Urgent')
                ->whereIn('status', TicketStatus::activeStatuses())
                ->count(),
            'agent_count'     => User::role('agent')->count(),
        ];
    }

    public function getStatusDistribution(): array
    {
        $data = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $counts = [];
        $colors = [];

        foreach ($data as $item) {
            $status = TicketStatus::tryFrom($item->status);
            if ($status) {
                $labels[] = $status->label();
                $colors[] = match ($item->status) {
                    'New' => '#0d6efd',
                    'Assigned' => '#0dcaf0',
                    'In Progress' => '#ffc107',
                    'Pending' => '#343a40',
                    'Resolved' => '#198754',
                    'Closed' => '#6c757d',
                    'Rejected' => '#dc3545',
                    'Reopen' => '#dc3545',
                    default => '#6c757d',
                };
            } else {
                $labels[] = $item->status;
                $colors[] = '#6c757d';
            }
            $counts[] = $item->total;
        }

        return compact('labels', 'counts', 'colors');
    }
}

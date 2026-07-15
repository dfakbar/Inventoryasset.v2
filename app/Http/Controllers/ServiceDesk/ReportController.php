<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('ticket.reports');

        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::whereIn('status', TicketStatus::activeStatuses())->count(),
            'resolved'    => Ticket::where('status', 'Resolved')->count(),
            'closed'      => Ticket::where('status', 'Closed')->count(),
            'breached'    => Ticket::whereNotNull('sla_resolve_at')
                ->where('sla_resolve_at', '<', now())
                ->whereNotIn('status', ['Closed', 'Resolved', 'Rejected'])
                ->count(),
            'urgent'      => Ticket::where('priority', 'Urgent')->whereNotIn('status', ['Closed', 'Resolved', 'Rejected'])->count(),
            'unassigned'  => Ticket::whereNull('agent_id')->where('status', 'New')->count(),
        ];

        $statusChart = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($item) => [
                'label' => TicketStatus::tryFrom($item->status)?->label() ?? $item->status,
                'total' => $item->total,
                'color' => TicketStatus::tryFrom($item->status)?->badgeClass() ?? 'secondary',
            ]);

        $priorityChart = Ticket::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->orderByRaw("FIELD(priority, 'Urgent', 'High', 'Medium', 'Low')")
            ->get();

        $monthlyTrend = Ticket::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->locale('id')->translatedFormat('M Y');
            $found = $monthlyTrend->firstWhere('month', $month);
            $trendData[] = [
                'label' => $label,
                'total' => $found ? $found->total : 0,
            ];
        }

        return view('service-desk.reports.index', compact(
            'stats', 'statusChart', 'priorityChart', 'trendData'
        ));
    }

    public function agentPerformance(): View
    {
        $this->authorize('ticket.reports');

        $agents = User::role('agent')
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => function ($q) {
                    $q->where('status', 'Resolved');
                },
                'assignedTickets as closed_count' => function ($q) {
                    $q->where('status', 'Closed');
                },
            ])
            ->withAvg('assignedTickets as avg_resolution_hours', DB::raw(
                "TIMESTAMPDIFF(HOUR, created_at, COALESCE(resolved_at, closed_at, NOW()))"
            ))
            ->get()
            ->map(function ($agent) {
                $total = $agent->total_assigned ?? 0;
                $resolved = $agent->resolved_count ?? 0;
                $agent->resolution_rate = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
                return $agent;
            });

        return view('service-desk.reports.agent-performance', compact('agents'));
    }
}

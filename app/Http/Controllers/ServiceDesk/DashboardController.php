<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $this->authorize('ticket.viewAny');

        $stats = [
            'total'          => Ticket::count(),
            'open'           => Ticket::whereIn('status', TicketStatus::activeStatuses())->count(),
            'unassigned'     => Ticket::whereNull('agent_id')->where('status', 'New')->count(),
            'resolved_today' => Ticket::whereDate('resolved_at', today())->count(),
            'sla_breached'   => Ticket::whereNotNull('sla_resolve_at')
                ->where('sla_resolve_at', '<', now())
                ->whereNotIn('status', ['Closed', 'Resolved', 'Rejected'])
                ->count(),
            'urgent'         => Ticket::where('priority', 'Urgent')
                ->whereIn('status', TicketStatus::activeStatuses())->count(),
            'agent_count'    => User::role('agent')->count(),
        ];

        $statusChart = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($item) => [
                'label' => TicketStatus::tryFrom($item->status)?->label() ?? $item->status,
                'total' => $item->total,
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

        $latestTickets = Ticket::with(['requester:id,name', 'agent:id,name', 'category:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return view('service-desk.dashboard', compact(
            'stats',
            'statusChart',
            'priorityChart',
            'trendData',
            'latestTickets',
        ));
    }
}

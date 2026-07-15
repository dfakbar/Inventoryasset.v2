<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMutationLog;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $hasAssetAccess = auth()->user()->can('asset.viewAny');
        $hasTicketAccess = auth()->user()->can('ticket.viewAny');

        // ── ASSET DATA ────────────────────────────────────────────────────
        $asset = [];
        if ($hasAssetAccess) {
            $statusCounts = Asset::selectRaw('status, count(*) as total')
                ->whereNotNull('status')
                ->groupBy('status')
                ->pluck('total', 'status');

            $inUse    = $statusCounts->get(AssetStatus::InUse->value, 0);
            $spare    = $statusCounts->get(AssetStatus::Spare->value, 0);
            $service  = $statusCounts->get(AssetStatus::Service->value, 0);
            $broken   = $statusCounts->get(AssetStatus::Broken->value, 0);
            $brokenChk = $statusCounts->get(AssetStatus::BrokenCheck->value, 0);
            $disposal = $statusCounts->get(AssetStatus::Disposal->value, 0);

            $stats = [
                'total_assets'    => Asset::count(),
                'in_use'          => $inUse,
                'spare'           => $spare,
                'service'         => $service,
                'broken'          => $broken,
                'broken_check'    => $brokenChk,
                'disposal'        => $disposal,
                'total_users'     => User::count(),
                'total_locations' => Location::count(),
                'total_value'     => Asset::whereNotNull('purchase_price')->sum('purchase_price'),
            ];
            $stats['problematic'] = $stats['service'] + $stats['broken'] + $stats['broken_check'];

            $statusChart = ['labels' => [], 'data' => [], 'colors' => []];
            $colorMap = [
                AssetStatus::InUse->value       => '#198754',
                AssetStatus::Spare->value        => '#0dcaf0',
                AssetStatus::Service->value      => '#ffc107',
                AssetStatus::Broken->value       => '#dc3545',
                AssetStatus::Disposal->value     => '#6c757d',
                AssetStatus::BrokenCheck->value  => '#343a40',
            ];
            foreach (AssetStatus::cases() as $status) {
                $count = $statusCounts->get($status->value, 0);
                if ($count > 0) {
                    $statusChart['labels'][] = $status->label();
                    $statusChart['data'][]   = $count;
                    $statusChart['colors'][] = $colorMap[$status->value];
                }
            }

            $categoryChart = Asset::select('asset_category_id', DB::raw('count(*) as total'))
                ->with('category:id,name,abbreviation')
                ->groupBy('asset_category_id')
                ->orderByDesc('total')
                ->limit(8)
                ->get()
                ->map(fn ($item) => [
                    'label' => $item->category?->abbreviation ?? $item->category?->name ?? 'N/A',
                    'total' => $item->total,
                ]);

            $latestAssets = Asset::with(['category', 'location', 'assignedUser'])
                ->latest()
                ->limit(5)
                ->get();

            $recentMutations = AssetMutationLog::with([
                    'asset:id,asset_code,name',
                    'performedBy:id,name',
                    'fromLocation:id,name',
                    'toLocation:id,name',
                    'fromAssignedUser:id,name',
                    'toAssignedUser:id,name',
                ])
                ->latest()
                ->limit(10)
                ->get();

            $mutationTrend = AssetMutationLog::select(
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
                $found = $mutationTrend->firstWhere('month', $month);
                $trendData[] = [
                    'label' => $label,
                    'total' => $found ? $found->total : 0,
                ];
            }

            $asset = compact('stats', 'statusChart', 'categoryChart', 'latestAssets', 'recentMutations', 'trendData');
        }

        // ── TICKET DATA ───────────────────────────────────────────────────
        $ticket = [];
        if ($hasTicketAccess) {
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
                    'label' => $item->status?->label() ?? $item->status,
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

            $ticket = compact('stats', 'statusChart', 'priorityChart', 'trendData', 'latestTickets');
        }

        return view('dashboard', compact('hasAssetAccess', 'hasTicketAccess', 'asset', 'ticket'));
    }
}

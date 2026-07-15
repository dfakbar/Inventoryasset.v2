<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\AutoAssignService;
use App\Services\SlaCalculator;
use App\Services\TicketLogService;
use App\Services\TicketStatusWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private readonly AutoAssignService $autoAssign,
        private readonly SlaCalculator $slaCalculator,
        private readonly TicketStatusWorkflow $workflow,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('ticket.viewAny');

        $user = auth()->user();
        $isRequester = !$user->isAdmin() && !$user->isAgent();
        $isAgent = $user->isAgent();
        $isAdmin = $user->isAdmin();

        $query = Ticket::with(['requester:id,name', 'agent:id,name', 'category:id,name']);

        if ($isRequester) {
            $query->mine();
        } elseif ($isAgent) {
            $query->where(function ($q) {
                $q->assignedToMe()->orWhere('requester_id', auth()->id());
            });
        }

        $tickets = $query
            ->search($request->input('search'))
            ->ofStatus($request->input('status'))
            ->ofPriority($request->input('priority'))
            ->ofCategory($request->integer('category_id') ?: null)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = TicketCategory::orderBy('name')->get();
        $statuses   = TicketStatus::cases();
        $priorities = TicketPriority::cases();

        return view('service-desk.tickets.index', compact('tickets', 'categories', 'statuses', 'priorities'));
    }

    public function statusView(Request $request): View
    {
        $this->authorize('ticket.viewAny');

        $user = auth()->user();
        $isRequester = !$user->isAdmin() && !$user->isAgent();
        $isAgent = $user->isAgent();

        $baseQuery = Ticket::with(['requester:id,name', 'agent:id,name', 'category:id,name']);

        if ($isRequester) {
            $baseQuery->mine();
        } elseif ($isAgent) {
            $baseQuery->where(function ($q) {
                $q->assignedToMe()->orWhere('requester_id', auth()->id());
            });
        }

        $groups = [
            'Open' => TicketStatus::activeStatuses(),
            'Ditunda' => [TicketStatus::Pending],
            'Terselesaikan' => [TicketStatus::Resolved],
            'Ditutup' => [TicketStatus::Closed],
            'Ditolak' => [TicketStatus::Rejected],
            'Konsep' => [TicketStatus::Draft],
        ];

        $grouped = [];
        foreach ($groups as $label => $statuses) {
            $statusValues = array_map(fn($s) => $s->value, $statuses);
            $grouped[$label] = (clone $baseQuery)
                ->whereIn('status', $statusValues)
                ->latest()
                ->take(20)
                ->get();
        }

        $summary = [];
        foreach ($groups as $label => $statuses) {
            $statusValues = array_map(fn($s) => $s->value, $statuses);
            $summary[$label] = (clone $baseQuery)
                ->whereIn('status', $statusValues)
                ->count();
        }

        return view('service-desk.tickets.status', compact('grouped', 'summary'));
    }

    public function create(): View
    {
        $this->authorize('ticket.create');

        $categories = TicketCategory::active()->orderBy('name')->get();
        $assets = Asset::where(function ($q) {
            $q->where('assigned_to', auth()->id())->orWhereNull('assigned_to');
        })->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $priorities = TicketPriority::cases();

        return view('service-desk.tickets.create', compact('categories', 'assets', 'locations', 'priorities'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->authorize('ticket.create');

        DB::beginTransaction();
        try {
            $data = $request->safe()->except([]);
            $data['requester_id'] = auth()->id();
            $data['status'] = 'New';
            $data['source'] = $data['source'] ?? 'Web';

            $ticket = Ticket::create($data);

            if ($request->boolean('auto_assign')) {
                $this->autoAssign->assign($ticket);
            }

            DB::commit();

            return redirect()
                ->route('sd.tickets.show', $ticket)
                ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat tiket.', ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Gagal membuat tiket. Silakan coba lagi.');
        }
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('ticket.viewAny');

        $ticket->load([
            'requester:id,name,email',
            'agent:id,name,email',
            'category:id,name,slug',
            'asset:id,asset_code,name',
            'location:id,name',
            'slaPolicy',
            'comments.user:id,name',
            'logs.user:id,name',
            'escalations.escalatedTo:id,name',
            'escalations.escalatedBy:id,name',
        ]);

        $user = auth()->user();
        $role = $user->isAdmin() ? 'admin' : ($user->isAgent() ? 'agent' : 'requester');
        $availableTransitions = $this->workflow->getAvailableTransitions($ticket, $role);
        $slaProgress = $this->slaCalculator->getSlaProgressPercent($ticket);
        $slaBreached = $this->slaCalculator->isSlaBreached($ticket);

        return view('service-desk.tickets.show', compact(
            'ticket', 'availableTransitions', 'slaProgress', 'slaBreached'
        ));
    }

    public function edit(Ticket $ticket): View
    {
        $this->authorize('ticket.manage');

        $categories = TicketCategory::active()->orderBy('name')->get();
        $assets = Asset::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $priorities = TicketPriority::cases();
        $agents = User::role('agent')->orderBy('name')->get();

        return view('service-desk.tickets.edit', compact(
            'ticket', 'categories', 'assets', 'locations', 'priorities', 'agents'
        ));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('ticket.manage');

        DB::beginTransaction();
        try {
            $data = $request->safe()->except([]);

            $ticket->update($data);

            DB::commit();

            return redirect()
                ->route('sd.tickets.show', $ticket)
                ->with('success', "Tiket {$ticket->ticket_number} berhasil diperbarui.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal update tiket ID: {$ticket->id}.", ['error' => $e->getMessage()]);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui tiket. Silakan coba lagi.');
        }
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('ticket.manage');

        $request->validate([
            'status' => ['required', 'string'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = $request->input('status');
        $user = auth()->user();
        $role = $user->isAdmin() ? 'admin' : ($user->isAgent() ? 'agent' : 'requester');

        if (!$this->workflow->canTransition($ticket, $newStatus, $role)) {
            return back()->with('error', 'Transisi status tidak diizinkan untuk role Anda.');
        }

        DB::beginTransaction();
        try {
            $oldStatus = $ticket->status->value;
            $ticket->status = $newStatus;
            $ticket->save();

            TicketLogService::statusChange($ticket, $oldStatus, $newStatus, $request->input('notes'));

            if ($newStatus === 'Assigned' && !$ticket->agent_id) {
                $this->autoAssign->assign($ticket);
            }

            DB::commit();

            $message = "Status tiket {$ticket->ticket_number} berhasil diubah dari {$oldStatus} menjadi {$newStatus}.";

            return redirect()->route('sd.tickets.show', $ticket)->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal update status tiket ID: {$ticket->id}.", ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal mengubah status tiket. Silakan coba lagi.');
        }
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!auth()->user()->isAgent() && !auth()->user()->can('ticket.assign') && !auth()->user()->can('ticket.manage')) {
            abort(403);
        }

        $request->validate([
            'agent_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $agent = User::findOrFail($request->agent_id);

        if (!$agent->isAgent()) {
            return back()->with('error', 'User yang dipilih bukan Agent.');
        }

        DB::beginTransaction();
        try {
            $oldAgentId = $ticket->agent_id;
            $ticket->agent_id = $agent->id;

            if ($ticket->status->value === 'New' || $ticket->status->value === 'Reopen') {
                $ticket->status = 'Assigned';
            }

            $ticket->save();

            TicketLogService::assignment($ticket, $oldAgentId, $agent->id);

            DB::commit();

            return redirect()
                ->route('sd.tickets.show', $ticket)
                ->with('success', "Tiket {$ticket->ticket_number} ditugaskan ke {$agent->name}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal assign tiket ID: {$ticket->id}.", ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menugaskan tiket. Silakan coba lagi.');
        }
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('ticket.delete');

        DB::beginTransaction();
        try {
            $ticketNumber = $ticket->ticket_number;
            $ticket->delete();
            DB::commit();

            return redirect()
                ->route('sd.tickets.index')
                ->with('success', "Tiket {$ticketNumber} berhasil dihapus.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal hapus tiket ID: {$ticket->id}.", ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus tiket. Silakan coba lagi.');
        }
    }
}

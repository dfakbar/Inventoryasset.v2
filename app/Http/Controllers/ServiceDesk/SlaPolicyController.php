<?php

namespace App\Http\Controllers\ServiceDesk;

use App\Enums\TicketPriority;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlaPolicyRequest;
use App\Http\Requests\UpdateSlaPolicyRequest;
use App\Models\SlaPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SlaPolicyController extends Controller
{
    public function index(): View
    {
        $this->authorize('ticket.reports');

        $policies = SlaPolicy::orderByRaw("FIELD(priority, 'Urgent', 'High', 'Medium', 'Low')")->get();

        return view('service-desk.sla-policies.index', compact('policies'));
    }

    public function create(): View
    {
        $this->authorize('ticket.reports');

        $priorities = TicketPriority::cases();
        $existing = SlaPolicy::pluck('priority')->toArray();

        return view('service-desk.sla-policies.create', compact('priorities', 'existing'));
    }

    public function store(StoreSlaPolicyRequest $request): RedirectResponse
    {
        $this->authorize('ticket.reports');

        DB::beginTransaction();
        try {
            SlaPolicy::create($request->validated());
            DB::commit();

            return redirect()
                ->route('sd.sla-policies.index')
                ->with('success', 'Kebijakan SLA berhasil ditambahkan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan SLA policy.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Gagal menyimpan kebijakan SLA.');
        }
    }

    public function edit(SlaPolicy $slaPolicy): View
    {
        $this->authorize('ticket.reports');

        $priorities = TicketPriority::cases();

        return view('service-desk.sla-policies.edit', compact('slaPolicy', 'priorities'));
    }

    public function update(UpdateSlaPolicyRequest $request, SlaPolicy $slaPolicy): RedirectResponse
    {
        $this->authorize('ticket.reports');

        DB::beginTransaction();
        try {
            $slaPolicy->update($request->validated());
            DB::commit();

            return redirect()
                ->route('sd.sla-policies.index')
                ->with('success', 'Kebijakan SLA berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update SLA policy.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Gagal memperbarui kebijakan SLA.');
        }
    }

    public function destroy(SlaPolicy $slaPolicy): RedirectResponse
    {
        $this->authorize('ticket.reports');

        DB::beginTransaction();
        try {
            $slaPolicy->delete();
            DB::commit();

            return redirect()
                ->route('sd.sla-policies.index')
                ->with('success', 'Kebijakan SLA berhasil dihapus.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal hapus SLA policy.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus kebijakan SLA.');
        }
    }
}

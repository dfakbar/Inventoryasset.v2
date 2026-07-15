@extends('layouts.app')

@section('title', 'Tiket ' . $ticket->ticket_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_number }}</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-lg-8">
        {{-- Ticket Header --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $ticket->subject }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="font-monospace small text-muted">{{ $ticket->ticket_number }}</span>
                            <span class="{{ $ticket->status->badgeClass() }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                <i class="bi {{ $ticket->status->icon() }}"></i>
                                {{ $ticket->status->label() }}
                            </span>
                            <span class="{{ $ticket->priority->badgeClass() }}">
                                <i class="bi bi-flag me-1"></i>{{ $ticket->priority->label() }}
                            </span>
                            @if ($slaBreached)
                                <span class="badge bg-danger">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>SLA Breach
                                </span>
                            @endif
                        </div>
                    </div>
                    <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                </div>

                {{-- SLA Progress --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Progress SLA</span>
                        <span class="{{ $slaProgress >= 100 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ min($slaProgress, 100) }}%
                        </span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar {{ $slaBreached ? 'bg-danger' : ($slaProgress >= 75 ? 'bg-warning' : 'bg-success') }}"
                             role="progressbar" style="width: {{ min($slaProgress, 100) }}%">
                        </div>
                    </div>
                    @if ($ticket->sla_resolve_at)
                        <small class="text-muted">
                            Deadline: {{ $ticket->sla_resolve_at->format('d/m/Y H:i') }}
                            @if ($ticket->sla_paused_total_minutes > 0)
                                (Paused: {{ $ticket->sla_paused_total_minutes }} menit)
                            @endif
                        </small>
                    @endif
                </div>

                {{-- Description --}}
                <div class="border-top pt-3">
                    <h6 class="fw-semibold mb-2"><i class="bi bi-card-text me-1"></i>Deskripsi</h6>
                    <p class="text-muted mb-0" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                </div>
            </div>
        </div>

        {{-- Comments --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-chat-dots me-1"></i>Komentar ({{ $ticket->comments->count() }})
                </h6>
            </div>
            <div class="card-body">
                @foreach ($ticket->comments as $comment)
                    <div class="d-flex gap-3 mb-3 {{ $comment->is_internal ? 'bg-warning bg-opacity-10 p-3 rounded' : '' }}">
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;font-size:.8rem;font-weight:700;color:#fff">
                            {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="small">{{ $comment->user?->name ?? 'User Dihapus' }}</strong>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            @if ($comment->is_internal)
                                <span class="badge bg-warning text-dark mb-1">
                                    <i class="bi bi-lock me-1"></i>Internal
                                </span>
                            @endif
                            <p class="mb-1 text-muted small" style="white-space: pre-wrap;">{{ $comment->body }}</p>
                            @if ($comment->attachment_path)
                                <a href="{{ Storage::url($comment->attachment_path) }}" target="_blank"
                                   class="small text-decoration-none">
                                    <i class="bi bi-paperclip me-1"></i>Lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                    @if (!$loop->last)
                        <hr class="my-3">
                    @endif
                @endforeach

                {{-- Add Comment Form --}}
                <form method="POST" action="{{ route('sd.tickets.comments.store', $ticket) }}" enctype="multipart/form-data" class="mt-3 border-top pt-3">
                    @csrf
                    <div class="mb-2">
                        <textarea name="body" rows="3" class="form-control @error('body') is-invalid @enderror"
                                  placeholder="Tulis komentar..." required>{{ old('body') }}</textarea>
                        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" name="attachment" class="form-control form-control-sm" style="max-width:200px">
                            @can('ticket.manage')
                            <div class="form-check mb-0">
                                <input type="checkbox" id="is_internal" name="is_internal" value="1" class="form-check-input">
                                <label for="is_internal" class="form-check-label small">Internal</label>
                            </div>
                            @endcan
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i>Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Activity Logs --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-1"></i>Aktivitas
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($ticket->logs as $log)
                        <li class="list-group-item px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong class="small">{{ $log->user?->name ?? 'System' }}</strong>
                                    <span class="text-muted small mx-1">—</span>
                                    <span class="small text-muted">{{ $log->action }}</span>
                                    @if ($log->field)
                                        <span class="small text-muted">
                                            ({{ $log->field }}: {{ $log->old_value ?? '—' }} → {{ $log->new_value ?? '—' }})
                                        </span>
                                    @endif
                                    @if ($log->notes)
                                        <br><span class="small text-muted">{{ $log->notes }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-3 text-muted">Belum ada aktivitas</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Actions --}}
        @if (!empty($availableTransitions))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-left-right me-1"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @foreach ($availableTransitions as $transition)
                        <form method="POST" action="{{ route('sd.tickets.status', $ticket) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $transition }}">
                            <button type="submit" class="btn btn-outline-primary w-100 text-start">
                                <i class="bi {{ \App\Enums\TicketStatus::tryFrom($transition)?->icon() ?? 'bi-arrow-right' }} me-2"></i>
                                {{ \App\Enums\TicketStatus::tryFrom($transition)?->label() ?? $transition }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-1"></i>Informasi Tiket</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted ps-3" style="width:100px">Requester</td>
                        <td class="fw-medium">{{ $ticket->requester?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Agent</td>
                        <td>
                            @if ($ticket->agent)
                                {{ $ticket->agent->name }}
                            @else
                                <span class="text-muted fst-italic">Belum ditugaskan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Kategori</td>
                        <td>{{ $ticket->category?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Prioritas</td>
                        <td><span class="{{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Status</td>
                        <td><span class="{{ $ticket->status->badgeClass() }}">{{ $ticket->status->label() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Sumber</td>
                        <td>{{ $ticket->source?->label() ?? 'Web' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Aset</td>
                        <td>{{ $ticket->asset?->asset_code ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Lokasi</td>
                        <td>{{ $ticket->location?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Dibuat</td>
                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if ($ticket->resolved_at)
                    <tr>
                        <td class="text-muted ps-3">Selesai</td>
                        <td>{{ $ticket->resolved_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if ($ticket->closed_at)
                    <tr>
                        <td class="text-muted ps-3">Ditutup</td>
                        <td>{{ $ticket->closed_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Assign Agent (Agent/Admin only) --}}
        @canany(['ticket.assign', 'ticket.manage'])
        @php
            $assignedAgentId = $ticket->agent_id;
            $currentUserId = auth()->id();
            $agents = \App\Models\User::role('agent')
                ->with('agentStatus')
                ->orderBy('name')
                ->get()
                ->sortByDesc(function ($a) use ($assignedAgentId) {
                    if ($a->id === $assignedAgentId) return 3;
                    if ($a->agentStatus && in_array($a->agentStatus->status->value, \App\Enums\AgentStatus::availableForAssignment())) return 2;
                    return 1;
                });
        @endphp
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-check me-1"></i>Tugaskan Agent</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->isAgent() && $assignedAgentId !== $currentUserId)
                <form method="POST" action="{{ route('sd.tickets.assign', $ticket) }}" class="mb-3">
                    @csrf
                    <input type="hidden" name="agent_id" value="{{ $currentUserId }}">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-person-plus me-1"></i>Assign ke Saya
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('sd.tickets.assign', $ticket) }}">
                    @csrf
                    <select name="agent_id" class="form-select form-select-sm mb-2" data-searchable required>
                        <option value="">— Pilih Agent —</option>
                        @foreach ($agents as $agent)
                            @php
                                $isAvailable = $agent->agentStatus && in_array($agent->agentStatus->status->value, \App\Enums\AgentStatus::availableForAssignment());
                            @endphp
                            <option value="{{ $agent->id }}" {{ $assignedAgentId === $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}@if(!$isAvailable && $assignedAgentId !== $agent->id) ({{ $agent->agentStatus?->status?->label() ?? 'Tidak tersedia' }})@endif
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-check2 me-1"></i>Assign
                    </button>
                </form>
            </div>
        </div>
        @endcanany

        {{-- Admin Actions --}}
        @can('ticket.delete')
        <div class="card shadow-sm border-0 border-danger mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Danger Zone</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('sd.tickets.destroy', $ticket) }}" method="POST"
                      onsubmit="return confirm('Hapus tiket {{ $ticket->ticket_number }}?\nTindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash me-1"></i>Hapus Tiket
                    </button>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection

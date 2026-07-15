@extends('layouts.app')

@section('title', 'Service Desk')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Service Desk</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-ticket-perforated-fill text-primary me-2"></i>Service Desk
        </h4>
        <p class="text-muted small mb-0 mt-1">Kelola seluruh tiket permintaan layanan IT</p>
    </div>
    @can('ticket.create')
    <a href="{{ route('sd.tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Buat Tiket Baru
    </a>
    @endcan
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <form method="GET" action="{{ route('sd.tickets.index') }}" id="filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="search" class="form-label small text-white-50 mb-1">
                        <i class="bi bi-search me-1"></i>Pencarian
                    </label>
                    <input type="text" id="search" name="search" class="form-control form-control-sm"
                           placeholder="Cari nomor tiket, subjek..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label for="status" class="form-label small text-white-50 mb-1">
                        <i class="bi bi-tag me-1"></i>Status
                    </label>
                    <select id="status" name="status" class="form-select form-select-sm" data-searchable>
                        <option value="">— Semua Status —</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="priority" class="form-label small text-white-50 mb-1">
                        <i class="bi bi-flag me-1"></i>Prioritas
                    </label>
                    <select id="priority" name="priority" class="form-select form-select-sm" data-searchable>
                        <option value="">— Semua Prioritas —</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ request('priority') === $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="category_id" class="form-label small text-white-50 mb-1">
                        <i class="bi bi-grid me-1"></i>Kategori
                    </label>
                    <select id="category_id" name="category_id" class="form-select form-select-sm" data-searchable>
                        <option value="">— Semua Kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-light btn-sm flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i>Cari
                    </button>
                    <a href="{{ route('sd.tickets.index') }}" class="btn btn-outline-light btn-sm flex-fill">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
            <span class="small text-muted">
                <i class="bi bi-collection me-1"></i>
                Total: <span class="fw-semibold text-dark">{{ $tickets->total() }}</span> tiket
                @if (request()->hasAny(['search', 'status', 'category_id']))
                    <span class="ms-2 badge bg-warning text-dark">
                        <i class="bi bi-funnel-fill me-1"></i>Filter aktif
                    </span>
                @endif
            </span>
            <span class="small text-muted">Halaman {{ $tickets->currentPage() }} dari {{ $tickets->lastPage() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:50px">#</th>
                        <th style="min-width:120px">Tiket</th>
                        <th style="min-width:200px">Subjek</th>
                        <th style="min-width:120px">Kategori</th>
                        <th style="min-width:130px">Requester</th>
                        <th style="min-width:130px">Agent</th>
                        <th class="text-center" style="min-width:90px">Prioritas</th>
                        <th class="text-center" style="min-width:120px">Status</th>
                        <th class="text-center" style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="text-center text-muted small">{{ $tickets->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="font-monospace fw-semibold small text-primary">
                                    {{ $ticket->ticket_number }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="fw-medium">{{ Str::limit($ticket->subject, 60) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                    {{ $ticket->category?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="small">{{ $ticket->requester?->name ?? '—' }}</td>
                            <td class="small">
                                @if ($ticket->agent)
                                    {{ $ticket->agent->name }}
                                @else
                                    <span class="text-muted fst-italic">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="{{ $ticket->priority->badgeClass() }}">
                                    {{ $ticket->priority->label() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="{{ $ticket->status->badgeClass() }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                    <i class="bi {{ $ticket->status->icon() }}"></i>
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('sd.tickets.show', $ticket) }}"
                                   class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2 opacity-30"></i>
                                <span class="fw-medium">Belum ada tiket.</span>
                                @if (request()->hasAny(['search', 'status', 'category_id']))
                                    <br><small>Coba ubah atau <a href="{{ route('sd.tickets.index') }}">hapus filter</a>.</small>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="d-flex justify-content-center py-3 border-top px-3">
                {{ $tickets->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

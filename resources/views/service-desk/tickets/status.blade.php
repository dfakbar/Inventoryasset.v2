@extends('layouts.app')

@section('title', 'Status Tiket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Status Tiket</li>
@endsection

@push('styles')
<style>
    .status-group-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 8px rgba(0,0,0,.06); }
    .status-group-card .card-header { border-bottom: none; padding: .75rem 1.25rem; font-weight: 600; }
    .status-group-card .card-body { padding: 0; }
    .status-group-card .table { margin-bottom: 0; }
    .status-group-card .table th { border-top: none; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; }
    .status-count { font-size: .85rem; font-weight: 700; padding: .15rem .65rem; border-radius: 20px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-grid-fill text-primary me-2"></i>Status Tiket
        </h4>
        <p class="text-muted small mb-0 mt-1">Tiket dikelompokkan berdasarkan status</p>
    </div>
    <a href="{{ route('sd.tickets.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-list-ul me-1"></i>Semua Tiket
    </a>
</div>

@php
    $groupMeta = [
        'Open' => ['color' => 'bg-primary', 'text' => 'text-white', 'icon' => 'bi-activity'],
        'Ditunda' => ['color' => 'bg-dark', 'text' => 'text-white', 'icon' => 'bi-pause-circle-fill'],
        'Terselesaikan' => ['color' => 'bg-success', 'text' => 'text-white', 'icon' => 'bi-check2-circle'],
        'Ditutup' => ['color' => 'bg-light', 'text' => 'text-dark', 'icon' => 'bi-lock-fill'],
        'Ditolak' => ['color' => 'bg-danger', 'text' => 'text-white', 'icon' => 'bi-x-octagon-fill'],
        'Konsep' => ['color' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'bi-file-earmark'],
    ];
@endphp

<div class="row g-3">
    @foreach ($grouped as $groupLabel => $tickets)
        @php $meta = $groupMeta[$groupLabel]; @endphp
        <div class="col-12">
            <div class="card status-group-card">
                <div class="card-header {{ $meta['color'] }} {{ $meta['text'] }} d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi {{ $meta['icon'] }} me-2"></i>{{ $groupLabel }}
                    </span>
                    <span class="status-count {{ $groupLabel === 'Ditutup' ? 'bg-dark text-white' : 'bg-white text-dark' }}">
                        {{ $summary[$groupLabel] }}
                    </span>
                </div>
                <div class="card-body">
                    @if ($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:120px">Tiket</th>
                                    <th style="min-width:180px">Subjek</th>
                                    <th style="min-width:120px">Kategori</th>
                                    <th style="min-width:120px">Requester</th>
                                    <th style="min-width:120px">Agent</th>
                                    <th class="text-center" style="min-width:80px">Prioritas</th>
                                    <th class="text-center" style="min-width:100px">Status</th>
                                    <th class="text-center" style="width:60px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-semibold small text-primary">{{ $ticket->ticket_number }}</span>
                                        <br>
                                        <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ Str::limit($ticket->subject, 50) }}</span>
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
                                            <span class="text-muted fst-italic">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $ticket->priority->badgeClass() }}">{{ $ticket->priority->label() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ $ticket->status->badgeClass() }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                            <i class="bi {{ $ticket->status->icon() }}"></i>
                                            {{ $ticket->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('sd.tickets.show', $ticket) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block mb-1" style="font-size:2rem;opacity:.3"></i>
                        <small>Tidak ada tiket {{ strtolower($groupLabel) }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

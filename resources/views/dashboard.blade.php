@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@push('styles')
<style>
    .metric-card {
        border: none;
        border-radius: 16px;
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
        position: relative;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(0,0,0,.12) !important;
    }
    .metric-card .metric-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .metric-card .metric-value {
        font-size: 2rem; font-weight: 800; line-height: 1.1;
        letter-spacing: -0.03em;
    }
    .metric-card .metric-label {
        font-size: .78rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .05em;
        opacity: .65;
    }
    .metric-card .metric-sub {
        font-size: .78rem; opacity: .55;
    }
    .chart-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,.07);
    }
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,.06);
        padding: 1rem 1.25rem .75rem;
    }
    .mutation-log-item {
        padding: .85rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
        transition: background .15s;
    }
    .mutation-log-item:hover { background: #f8f9fa; }
    .mutation-log-item:last-child { border-bottom: none; }
    .mutation-arrow {
        display: inline-flex; align-items: center;
        font-size: .78rem; color: #6c757d;
    }
    .mutation-arrow i { font-size: .9rem; color: #0d6efd; }
    .recent-assets-table td, .recent-assets-table th {
        padding: .65rem 1rem;
        vertical-align: middle;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .6; transform: scale(.85); }
    }
    .pulse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        animation: pulse-dot 2s infinite;
        display: inline-block;
        flex-shrink: 0;
    }
    .page-header-greeting {
        font-size: 1.45rem; font-weight: 800; color: #1a1a2e;
        letter-spacing: -.02em;
    }
    .page-header-sub {
        font-size: .875rem; color: #6c757d;
    }
    .section-divider {
        border: 0;
        border-top: 2px dashed #dee2e6;
        margin: 2rem 0;
        opacity: .5;
    }
    .list-group-ticket .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: .75rem 1.25rem;
    }
    .list-group-ticket .list-group-item:first-child {
        border-top: 0;
    }
    .list-group-ticket .list-group-item:last-child {
        border-bottom: 0;
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ── --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <p class="page-header-greeting mb-0">
            Selamat datang, {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </p>
        <p class="page-header-sub mb-0">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @can('asset.create')
        <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Aset
        </a>
        @endcan
        @can('ticket.create')
        <a href="{{ route('sd.tickets.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Buat Tiket
        </a>
        @endcan
    </div>
</div>

@if($hasAssetAccess && $hasTicketAccess)
{{-- ── Nav Pills: User with both permissions ── --}}
<ul class="nav nav-pills mb-4 gap-1" id="dashboardTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="asset-tab" data-bs-toggle="pill" data-bs-target="#asset-section" type="button" role="tab">
            <i class="bi bi-box-seam-fill me-1"></i>Dashboard Aset
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="ticket-tab" data-bs-toggle="pill" data-bs-target="#ticket-section" type="button" role="tab">
            <i class="bi bi-ticket-perforated-fill me-1"></i>Dashboard Service Desk
        </button>
    </li>
</ul>
@endif

{{-- ── Tab Content ── --}}
<div class="tab-content" id="dashboardTabContent">

@if($hasTicketAccess)
<div class="tab-pane {{ $hasAssetAccess && $hasTicketAccess ? '' : 'show active' }}" id="ticket-section" role="tabpanel">
    @include('dashboard._ticket', ['ticket' => $ticket])
</div>
@endif

@if($hasAssetAccess)
<div class="tab-pane {{ $hasAssetAccess && !$hasTicketAccess ? 'show active' : ($hasAssetAccess && $hasTicketAccess ? 'show active' : '') }}" id="asset-section" role="tabpanel">
    @include('dashboard._asset', ['asset' => $asset])
</div>
@endif

</div>

@endsection



@extends('layouts.app')

@section('title', 'Laporan Service Desk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
@endsection

@section('content')
<div>
    <h4 class="fw-bold mb-4">
        <i class="bi bi-bar-chart-fill text-primary me-2"></i>Laporan Service Desk
    </h4>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-primary">{{ $stats['total'] }}</div>
                <small class="text-muted">Total Tiket</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-warning">{{ $stats['open'] }}</div>
                <small class="text-muted">Tiket Terbuka</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-success">{{ $stats['resolved'] }}</div>
                <small class="text-muted">Terselesaikan</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-danger">{{ $stats['breached'] }}</div>
                <small class="text-muted">SLA Breach</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-info">{{ $stats['unassigned'] }}</div>
                <small class="text-muted">Belum Ditugaskan</small>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold text-danger">{{ $stats['urgent'] }}</div>
                <small class="text-muted">Urgent Open</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Status Distribution --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-1"></i>Distribusi Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        {{-- Priority Distribution --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-1"></i>Distribusi Prioritas</h6>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" height="250"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up me-1"></i>Tren Tiket 6 Bulan</h6>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Status Chart (Doughnut)
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statusChart->pluck('label')),
                datasets: [{
                    data: @json($statusChart->pluck('total')),
                    backgroundColor: [
                        '#0d6efd', '#0dcaf0', '#ffc107', '#343a40',
                        '#198754', '#6c757d', '#dc3545', '#dc3545'
                    ],
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Priority Chart (Bar)
    const priorityCtx = document.getElementById('priorityChart');
    if (priorityCtx) {
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: @json($priorityChart->pluck('priority')),
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: @json($priorityChart->pluck('total')),
                    backgroundColor: ['#dc3545', '#ffc107', '#0d6efd', '#198754'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // Trend Chart (Line)
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($trendData, 'label')),
                datasets: [{
                    label: 'Tiket Baru',
                    data: @json(array_column($trendData, 'total')),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
});
</script>
@endpush

@php $s = $ticket['stats']; $statusChart = $ticket['statusChart']; $priorityChart = $ticket['priorityChart']; $trendData = $ticket['trendData']; $latestTickets = $ticket['latestTickets']; @endphp

{{-- ── Ticket Metric Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#eef2ff,#f0f7ff)">
            <div class="card-body p-3">
                <div class="metric-icon bg-primary bg-opacity-15 mb-3">
                    <i class="bi bi-collection-fill text-primary"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['total'] }}</div>
                <div class="metric-label text-primary">Total Tiket</div>
                <div class="metric-sub mt-1">Semua tiket</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#fffbeb,#fef3c7)">
            <div class="card-body p-3">
                <div class="metric-icon bg-warning bg-opacity-15 mb-3">
                    <i class="bi bi-activity text-warning"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['open'] }}</div>
                <div class="metric-label text-warning">Terbuka</div>
                <div class="metric-sub mt-1">Belum selesai</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#fef2f2,#fecaca)">
            <div class="card-body p-3">
                <div class="metric-icon bg-danger bg-opacity-15 mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['sla_breached'] }}</div>
                <div class="metric-label text-danger">SLA Breach</div>
                <div class="metric-sub mt-1">Perlu eskalasi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#f0fdf4,#dcfce7)">
            <div class="card-body p-3">
                <div class="metric-icon bg-success bg-opacity-15 mb-3">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['resolved_today'] }}</div>
                <div class="metric-label text-success">Selesai Hari Ini</div>
                <div class="metric-sub mt-1">Tiket resolved</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#eff6ff,#dbeafe)">
            <div class="card-body p-3">
                <div class="metric-icon bg-info bg-opacity-15 mb-3">
                    <i class="bi bi-person-plus text-info"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['unassigned'] }}</div>
                <div class="metric-label text-info">Belum Ditugaskan</div>
                <div class="metric-sub mt-1">Butuh agent</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#fff1f2,#ffe4e6)">
            <div class="card-body p-3">
                <div class="metric-icon bg-danger bg-opacity-25 mb-3">
                    <i class="bi bi-bell-fill text-danger"></i>
                </div>
                <div class="metric-value text-dark">{{ $s['urgent'] }}</div>
                <div class="metric-label text-danger">Urgent</div>
                <div class="metric-sub mt-1">{{ $s['agent_count'] }} agent</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Ticket Charts ── --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-1"></i>Distribusi Status</h6>
            </div>
            <div class="card-body">
                <canvas id="ticketStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-1"></i>Distribusi Prioritas</h6>
            </div>
            <div class="card-body">
                <canvas id="ticketPriorityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Trend + Latest Tickets ── --}}
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up me-1"></i>Tren Tiket 6 Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="ticketTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-1"></i>Tiket Terbaru</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush list-group-ticket">
                    @forelse ($latestTickets as $ticketItem)
                        <li class="list-group-item px-3 py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('sd.tickets.show', $ticketItem) }}" class="text-decoration-none fw-medium small">
                                        {{ Str::limit($ticketItem->subject, 45) }}
                                    </a>
                                    <br>
                                    <span class="font-monospace small text-muted">{{ $ticketItem->ticket_number }}</span>
                                    <span class="{{ $ticketItem->status->badgeClass() }} ms-1" style="font-size:.65rem;">
                                        {{ $ticketItem->status->label() }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $ticketItem->created_at->diffForHumans() }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted">
                            <i class="bi bi-inbox d-block mb-1 display-6"></i>
                            <small>Belum ada tiket</small>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    var statusData = @json($statusChart);
    var priorityData = @json($priorityChart);

    new Chart(document.getElementById('ticketStatusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(function(d) { return d.label; }),
            datasets: [{
                data: statusData.map(function(d) { return d.total; }),
                backgroundColor: ['#0d6efd','#0dcaf0','#ffc107','#343a40','#198754','#6c757d','#dc3545','#dc3545'],
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('ticketPriorityChart'), {
        type: 'bar',
        data: {
            labels: priorityData.map(function(d) { return d.priority; }),
            datasets: [{
                label: 'Jumlah Tiket',
                data: priorityData.map(function(d) { return d.total; }),
                backgroundColor: ['#dc3545','#ffc107','#0d6efd','#198754'],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('ticketTrendChart'), {
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
})();
</script>
@endpush

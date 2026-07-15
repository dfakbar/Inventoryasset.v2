@php $s = $asset['stats']; $statusChart = $asset['statusChart']; $categoryChart = $asset['categoryChart']; $latestAssets = $asset['latestAssets']; $recentMutations = $asset['recentMutations']; $trendData = $asset['trendData']; @endphp

{{-- ── Asset Metric Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#eef2ff,#f0f7ff)">
            <div class="card-body p-3">
                <div class="metric-icon bg-primary bg-opacity-15 mb-3">
                    <i class="bi bi-box-seam-fill text-primary"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['total_assets']) }}</div>
                <div class="metric-label text-primary">Total Aset</div>
                <div class="metric-sub mt-1">Semua inventaris</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#ecfdf5,#d1fae5)">
            <div class="card-body p-3">
                <div class="metric-icon bg-success bg-opacity-15 mb-3">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['in_use']) }}</div>
                <div class="metric-label text-success">Digunakan</div>
                <div class="metric-sub mt-1">
                    @if($s['total_assets'] > 0)
                        {{ round($s['in_use'] / $s['total_assets'] * 100) }}% dari total
                    @else
                        0% dari total
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#eff6ff,#dbeafe)">
            <div class="card-body p-3">
                <div class="metric-icon bg-info bg-opacity-15 mb-3">
                    <i class="bi bi-archive-fill text-info"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['spare']) }}</div>
                <div class="metric-label text-info">Cadangan</div>
                <div class="metric-sub mt-1">Siap pakai</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#fffbeb,#fef3c7)">
            <div class="card-body p-3">
                <div class="metric-icon bg-warning bg-opacity-15 mb-3">
                    <i class="bi bi-tools text-warning"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['service']) }}</div>
                <div class="metric-label text-warning">Servis</div>
                <div class="metric-sub mt-1">Dalam perbaikan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#fff1f2,#ffe4e6)">
            <div class="card-body p-3">
                <div class="metric-icon bg-danger bg-opacity-15 mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['problematic']) }}</div>
                <div class="metric-label text-danger">Bermasalah</div>
                <div class="metric-sub mt-1">Perlu perhatian</div>
            </div>
        </div>
    </div>
    @can('asset.manage_finances')
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#f0fdf4,#dcfce7)">
            <div class="card-body p-3">
                <div class="metric-icon bg-success bg-opacity-15 mb-3">
                    <i class="bi bi-currency-dollar text-success"></i>
                </div>
                <div class="metric-value text-dark" style="font-size: 1.25rem;">
                    Rp {{ number_format($s['total_value'], 0, ',', '.') }}
                </div>
                <div class="metric-label text-success">Nilai Aset</div>
                <div class="metric-sub mt-1">Akumulasi harga</div>
            </div>
        </div>
    </div>
    @else
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card metric-card shadow-sm h-100" style="background: linear-gradient(135deg,#faf5ff,#f3e8ff)">
            <div class="card-body p-3">
                <div class="metric-icon bg-purple bg-opacity-15 mb-3" style="background: rgba(111,66,193,.12)">
                    <i class="bi bi-people-fill" style="color: #6f42c1"></i>
                </div>
                <div class="metric-value text-dark">{{ number_format($s['total_users']) }}</div>
                <div class="metric-label" style="color:#6f42c1">Pengguna</div>
                <div class="metric-sub mt-1">{{ $s['total_locations'] }} lokasi</div>
            </div>
        </div>
    </div>
    @endcan
</div>

{{-- ── Asset Charts ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi Status Aset
                </h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                @if(count($statusChart['data']) > 0)
                <div style="position: relative; width: 200px; height: 200px;">
                    <canvas id="statusDoughnutChart"></canvas>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                        <div style="font-size:1.6rem;font-weight:800;color:#1a1a2e;">{{ $s['total_assets'] }}</div>
                        <div style="font-size:.7rem;color:#6c757d;font-weight:600;text-transform:uppercase;">Total</div>
                    </div>
                </div>
                <div class="mt-3 w-100 px-2">
                    @foreach($statusChart['labels'] as $i => $label)
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="pulse-dot" style="background: {{ $statusChart['colors'][$i] }}"></span>
                            <span style="font-size:.8rem;">{{ $label }}</span>
                        </div>
                        <span class="badge" style="background:{{ $statusChart['colors'][$i] }}; font-size:.75rem;">
                            {{ $statusChart['data'][$i] }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-pie-chart display-4 opacity-25 d-block mb-2"></i>
                    <small>Belum ada data aset</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card chart-card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-bar-chart-fill text-success me-2"></i>Distribusi Per Kategori
                </h6>
            </div>
            <div class="card-body">
                @if($categoryChart->count() > 0)
                <canvas id="categoryBarChart" style="max-height: 240px;"></canvas>
                @else
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart display-4 opacity-25 d-block mb-2"></i>
                        <small>Belum ada data kategori</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Trend Mutasi + Log Mutasi ── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card chart-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-graph-up-arrow text-primary me-2"></i>Trend Mutasi (6 Bulan)
                </h6>
                <span class="badge bg-primary bg-opacity-10 text-primary small">Terakhir 6 Bulan</span>
            </div>
            <div class="card-body">
                <canvas id="mutationTrendChart" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card chart-card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-arrow-left-right text-warning me-2"></i>Log Mutasi Terbaru
                </h6>
                <span class="badge bg-warning bg-opacity-15 text-warning-emphasis small">Real-time</span>
            </div>
            <div class="card-body p-0" style="max-height: 320px; overflow-y: auto;">
                @forelse($recentMutations as $log)
                <div class="mutation-log-item">
                    <div class="d-flex align-items-start gap-2">
                        <div class="flex-shrink-0 mt-1">
                            <span class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                  style="width:32px;height:32px">
                                <i class="bi bi-arrow-left-right text-primary small"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-dark">
                                <a href="{{ route('assets.show', $log->asset_id) }}"
                                   class="text-decoration-none text-dark">
                                    <span class="font-monospace text-primary">{{ $log->asset?->asset_code }}</span>
                                    — {{ $log->asset?->name }}
                                </a>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @if($log->from_location_id !== $log->to_location_id)
                                <div class="mutation-arrow">
                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                    <span>{{ $log->fromLocation?->name ?? '—' }}</span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="fw-semibold text-dark">{{ $log->toLocation?->name ?? '—' }}</span>
                                </div>
                                @endif
                                @if($log->from_status !== $log->to_status)
                                <div class="mutation-arrow">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>
                                    <span>{{ $log->from_status ?? '—' }}</span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="fw-semibold text-dark">{{ $log->to_status ?? '—' }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <small class="text-muted">
                                    <i class="bi bi-person-fill me-1"></i>{{ $log->performedBy?->name ?? 'Sistem' }}
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                                </small>
                                @if($log->mutation_date)
                                <small class="text-muted">
                                    <i class="bi bi-calendar3-event me-1"></i>{{ $log->mutation_date->translatedFormat('d M Y') }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="d-flex align-items-center justify-content-center py-5 text-muted">
                    <div class="text-center">
                        <i class="bi bi-inbox display-4 opacity-25 d-block mb-2"></i>
                        <small>Belum ada log mutasi.</small>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── Aset Terbaru ── --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card chart-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history text-secondary me-2"></i>Aset Baru Ditambahkan
                </h6>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table recent-assets-table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:130px">Kode Aset</th>
                                <th style="min-width:180px">Nama</th>
                                <th style="min-width:120px">Kategori</th>
                                <th style="min-width:140px">Lokasi</th>
                                <th class="text-center" style="min-width:120px">Status</th>
                                <th style="min-width:120px">Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestAssets as $assetItem)
                            <tr onclick="window.location='{{ route('assets.show', $assetItem) }}'" style="cursor:pointer">
                                <td>
                                    <a href="{{ route('assets.show', $assetItem) }}" class="font-monospace fw-semibold small text-primary text-decoration-none">
                                        {{ $assetItem->asset_code }}
                                    </a>
                                </td>
                                <td class="fw-medium">{{ $assetItem->name }}</td>
                                <td>
                                    @if($assetItem->category)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                        {{ $assetItem->category->abbreviation ?? $assetItem->category->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $assetItem->location?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="{{ $assetItem->status->badgeClass() }} d-inline-flex align-items-center gap-1 px-2 py-1">
                                        <i class="bi {{ $assetItem->status->icon() }}"></i>
                                        {{ $assetItem->status->label() }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $assetItem->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-2 opacity-25"></i>
                                    <span>Belum ada aset terdaftar.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(count($statusChart['data']) > 0)
(function() {
    var ctx = document.getElementById('statusDoughnutChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($statusChart['labels']),
            datasets: [{
                data: @json($statusChart['data']),
                backgroundColor: @json($statusChart['colors']),
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
            }]
        },
        options: {
            cutout: '72%',
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed + ' aset'; }
                    }
                }
            },
            animation: { animateScale: true, duration: 900 }
        }
    });
})();
@endif

@if($categoryChart->count() > 0)
(function() {
    var ctx = document.getElementById('categoryBarChart');
    if (!ctx) return;
    var labels = @json($categoryChart->pluck('label'));
    var data   = @json($categoryChart->pluck('total'));
    var colors = ['#0d6efd','#198754','#0dcaf0','#ffc107','#6f42c1','#fd7e14','#dc3545','#20c997'];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Aset',
                data: data,
                backgroundColor: colors.slice(0, data.length).map(function(c) { return c + '22'; }),
                borderColor: colors.slice(0, data.length),
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ' ' + ctx.parsed.y + ' aset'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            animation: { duration: 800 }
        }
    });
})();
@endif

(function() {
    var ctx = document.getElementById('mutationTrendChart');
    if (!ctx) return;
    var trendData = @json($trendData);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(function(d) { return d.label; }),
            datasets: [{
                label: 'Mutasi',
                data: trendData.map(function(d) { return d.total; }),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return ' ' + ctx.parsed.y + ' mutasi'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            animation: { duration: 900 }
        }
    });
})();
</script>
@endpush

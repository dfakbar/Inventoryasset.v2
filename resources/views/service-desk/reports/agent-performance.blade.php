@extends('layouts.app')

@section('title', 'Performa Agent')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sd.reports.index') }}" class="text-decoration-none">Laporan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Performa Agent</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Performa Agent</h4>
        <p class="text-muted small mb-0 mt-1">Rekapitulasi kinerja tim IT Support</p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Agent</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Total Ditugaskan</th>
                        <th class="text-center">Terselesaikan</th>
                        <th class="text-center">Tingkat Resolusi</th>
                        <th class="text-center">Rata-rata Waktu Resolusi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($agents as $agent)
                        <tr>
                            <td class="fw-medium">{{ $agent->name }}</td>
                            <td class="text-center small">{{ $agent->email }}</td>
                            <td class="text-center">{{ $agent->total_assigned ?? 0 }}</td>
                            <td class="text-center">{{ $agent->resolved_count ?? 0 }}</td>
                            <td class="text-center">
                                <span class="badge {{ $agent->resolution_rate >= 80 ? 'bg-success' : ($agent->resolution_rate >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $agent->resolution_rate }}%
                                </span>
                            </td>
                            <td class="text-center text-muted">
                                {{ $agent->avg_resolution_hours ? round($agent->avg_resolution_hours, 1) . ' jam' : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada data agent.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Kebijakan SLA')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kebijakan SLA</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-clock-fill text-primary me-2"></i>Kebijakan SLA</h4>
        <p class="text-muted small mb-0 mt-1">Atur target waktu respons dan resolusi tiket</p>
    </div>
    @if (\App\Models\SlaPolicy::count() < 4)
    <a href="{{ route('sd.sla-policies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kebijakan
    </a>
    @endif
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Prioritas</th>
                        <th class="text-center">Respon (Jam)</th>
                        <th class="text-center">Resolusi (Jam)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Eskalasi (Menit)</th>
                        <th class="text-center" style="width:150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($policies as $policy)
                        <tr>
                            <td>
                                <span class="badge fs-6 {{ match($policy->priority) {
                                    'Urgent' => 'bg-danger',
                                    'High' => 'bg-warning text-dark',
                                    'Medium' => 'bg-primary',
                                    'Low' => 'bg-success',
                                    default => 'bg-secondary'
                                } }}">
                                    {{ $policy->priority }}
                                </span>
                            </td>
                            <td class="text-center fw-medium">{{ $policy->respond_hours }}</td>
                            <td class="text-center fw-medium">{{ $policy->resolve_hours }}</td>
                            <td class="text-center">
                                @if ($policy->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $policy->escalate_minutes }}</td>
                            <td class="text-center">
                                <a href="{{ route('sd.sla-policies.edit', $policy) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('sd.sla-policies.destroy', $policy) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Hapus kebijakan SLA {{ $policy->priority }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Belum ada kebijakan SLA. Tambahkan untuk prioritas Urgent, High, Medium, dan Low.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

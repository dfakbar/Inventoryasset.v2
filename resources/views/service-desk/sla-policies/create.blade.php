@extends('layouts.app')

@section('title', 'Tambah Kebijakan SLA')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sd.sla-policies.index') }}" class="text-decoration-none">SLA</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Kebijakan SLA</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sd.sla-policies.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="priority" class="form-label fw-medium">Prioritas <span class="text-danger">*</span></label>
                    <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach ($priorities as $p)
                            <option value="{{ $p->value }}" {{ in_array($p->value, $existing) ? 'disabled' : '' }}
                                {{ old('priority') === $p->value ? 'selected' : '' }}>
                                {{ $p->label() }} {{ in_array($p->value, $existing) ? '(sudah ada)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="respond_hours" class="form-label fw-medium">Respon (Jam) <span class="text-danger">*</span></label>
                    <input type="number" id="respond_hours" name="respond_hours"
                           class="form-control @error('respond_hours') is-invalid @enderror"
                           value="{{ old('respond_hours') }}" min="1" required>
                    @error('respond_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="resolve_hours" class="form-label fw-medium">Resolusi (Jam) <span class="text-danger">*</span></label>
                    <input type="number" id="resolve_hours" name="resolve_hours"
                           class="form-control @error('resolve_hours') is-invalid @enderror"
                           value="{{ old('resolve_hours') }}" min="1" required>
                    @error('resolve_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="escalate_minutes" class="form-label fw-medium">Eskalasi (menit setelah breach)</label>
                    <input type="number" id="escalate_minutes" name="escalate_minutes"
                           class="form-control @error('escalate_minutes') is-invalid @enderror"
                           value="{{ old('escalate_minutes', 30) }}" min="0">
                    @error('escalate_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" checked>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('sd.sla-policies.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

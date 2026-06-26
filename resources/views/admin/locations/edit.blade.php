@extends('layouts.app')

@section('title', 'Edit Lokasi — ' . $location->name)

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Admin</li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.locations.index') }}" class="text-decoration-none text-muted">Manajemen Lokasi</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-pencil-square text-warning me-2"></i>Edit Lokasi
        </h4>
        <p class="text-muted small mb-0 mt-1">Perbarui data lokasi: <strong>{{ $location->name }}</strong></p>
    </div>
    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
        <div>
            <strong>{{ $errors->count() }} kesalahan:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-7 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning py-2 px-4">
                <h6 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-geo-alt me-2"></i>Detail Lokasi
                </h6>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.locations.update', $location) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Nama Lokasi --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $location->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Departemen --}}
                    <div class="mb-3">
                        <label for="department" class="form-label fw-semibold small">Departemen</label>
                        <input type="text"
                               id="department"
                               name="department"
                               class="form-control {{ $errors->has('department') ? 'is-invalid' : '' }}"
                               value="{{ old('department', $location->department) }}"
                               placeholder="Contoh: IT, Keuangan, HRD">
                        <div class="form-text text-muted small">
                            <i class="bi bi-diagram-3 me-1"></i>Departemen atau divisi yang bertanggung jawab.
                        </div>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold small">Deskripsi</label>
                        <textarea id="description"
                                  name="description"
                                  class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                  rows="3"
                                  placeholder="Deskripsi singkat mengenai lokasi ini...">{{ old('description', $location->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-floppy2 me-1"></i>Perbarui Lokasi
                        </button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

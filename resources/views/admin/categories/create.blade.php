@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Admin</li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.categories.index') }}" class="text-decoration-none text-muted">Manajemen Kategori</a>
    </li>
    <li class="breadcrumb-item active">Tambah Kategori</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-tag-fill text-primary me-2"></i>Tambah Kategori
        </h4>
        <p class="text-muted small mb-0 mt-1">Tambahkan kategori/jenis aset baru.</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
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
            <div class="card-header bg-primary text-white py-2 px-4">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-tag me-2"></i>Detail Kategori
                </h6>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.categories.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name') }}"
                               placeholder="Contoh: Monitor, Laptop, Printer"
                               required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="abbreviation" class="form-label fw-semibold small">
                            Singkatan <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="abbreviation"
                               name="abbreviation"
                               class="form-control {{ $errors->has('abbreviation') ? 'is-invalid' : '' }} font-monospace"
                               value="{{ old('abbreviation') }}"
                               placeholder="Contoh: MON, LPT, PRT"
                               style="text-transform: uppercase"
                               required>
                        <div class="form-text text-muted small">
                            <i class="bi bi-info-circle me-1"></i>Kode singkat 3-10 karakter untuk kode aset (contoh: <code>MON</code> untuk Monitor).
                        </div>
                        @error('abbreviation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold small">Deskripsi</label>
                        <textarea id="description"
                                  name="description"
                                  class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                  rows="3"
                                  placeholder="Deskripsi singkat mengenai kategori ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy2 me-1"></i>Simpan Kategori
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

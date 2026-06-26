@extends('layouts.app')

@section('title', 'Edit Aset — ' . $asset->asset_code)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-box-seam me-1"></i>Manajemen Aset
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none text-muted font-monospace">
            {{ $asset->asset_code }}
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-pencil-square text-warning me-2"></i>Edit Aset
        </h4>
        <p class="text-muted small mb-0 mt-1">
            Memperbarui data aset
            <span class="font-monospace fw-semibold text-dark">{{ $asset->asset_code }}</span>
            — {{ $asset->name }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-1"></i>Lihat Detail
        </a>
        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>
</div>

{{-- Warning: kode aset tidak dapat diubah --}}
<div class="alert alert-warning d-flex align-items-start gap-2 mb-4 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Kode Aset Terkunci</strong><br>
        <span class="small">Kode aset tidak dapat diubah untuk menjaga integritas histori dan pencatatan sistem.</span>
    </div>
</div>

{{-- Form Card --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-warning bg-opacity-75 py-3">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="bi bi-pencil-square me-2"></i>Edit Aset:
            <code class="ms-1 fs-6 bg-white bg-opacity-75 px-2 py-1 rounded">{{ $asset->asset_code }}</code>
        </h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('assets.update', $asset) }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate>
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>Terdapat {{ $errors->count() }} kesalahan pada formulir:</strong>
                        <ul class="mb-0 mt-1 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Read-only: Kode Aset --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">
                    <i class="bi bi-upc-scan me-1"></i>Kode Aset
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="bi bi-lock-fill small"></i>
                    </span>
                    <input type="text"
                           class="form-control font-monospace bg-light text-muted border-start-0"
                           value="{{ $asset->asset_code }}"
                           disabled
                           aria-label="Kode Aset (tidak dapat diubah)">
                </div>
                <div class="form-text text-muted small">
                    <i class="bi bi-shield-lock me-1"></i>Kode aset dikunci dan tidak dapat diubah.
                </div>
            </div>

            <hr class="mb-4">

            @include('assets._form', ['asset' => $asset])

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-floppy2 me-1"></i>Perbarui Aset
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

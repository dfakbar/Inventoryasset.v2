@extends('layouts.app')

@section('title', 'Tambah Aset Baru')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('assets.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-box-seam me-1"></i>Manajemen Aset
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Aset Baru</li>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Aset Baru
        </h4>
        <p class="text-muted small mb-0 mt-1">Isi formulir di bawah untuk mendaftarkan aset baru ke sistem.</p>
    </div>
    <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
    </a>
</div>

{{-- Info: kode otomatis --}}
<div class="alert alert-info d-flex align-items-start gap-2 mb-4 shadow-sm" role="alert">
    <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Kode Aset Otomatis</strong><br>
        <span class="small">Kode aset akan digenerate otomatis oleh sistem berdasarkan kategori dan urutan pendaftaran.</span>
    </div>
</div>

{{-- Form Card --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-plus-circle me-2"></i>Tambah Aset Baru
        </h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('assets.store') }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate>
            @csrf

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

            @include('assets._form')

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy2 me-1"></i>Simpan Aset
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

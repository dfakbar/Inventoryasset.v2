@extends('layouts.app')

@section('title', 'Tambah Kategori Tiket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sd.categories.index') }}" class="text-decoration-none">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori Tiket</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sd.categories.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-medium">Nama <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                    <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug') }}" required placeholder="contoh: hardware-issue">
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="parent_id" class="form-label fw-medium">Induk Kategori</label>
                    <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror" data-searchable>
                        <option value="">— Tidak ada (Kategori Utama) —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" checked>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('sd.categories.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

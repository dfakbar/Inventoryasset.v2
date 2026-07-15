@extends('layouts.app')

@section('title', 'Buat Tiket Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buat Tiket</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Buat Tiket Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sd.tickets.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label for="subject" class="form-label fw-medium">Judul Tiket <span class="text-danger">*</span></label>
                    <input type="text" id="subject" name="subject"
                           class="form-control @error('subject') is-invalid @enderror"
                           value="{{ old('subject') }}" placeholder="Contoh: Laptop tidak bisa menyala" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
                    <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror" data-searchable required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="priority" class="form-label fw-medium">Prioritas <span class="text-danger">*</span></label>
                    <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror">
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ old('priority', 'Medium') === $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="source" class="form-label fw-medium">Sumber</label>
                    <select id="source" name="source" class="form-select @error('source') is-invalid @enderror">
                        <option value="Web" {{ old('source', 'Web') === 'Web' ? 'selected' : '' }}>Website</option>
                        <option value="Email" {{ old('source') === 'Email' ? 'selected' : '' }}>Email</option>
                        <option value="WhatsApp" {{ old('source') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="Phone" {{ old('source') === 'Phone' ? 'selected' : '' }}>Telepon</option>
                    </select>
                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="asset_id" class="form-label fw-medium">Aset Terkait</label>
                    <select id="asset_id" name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" data-searchable>
                        <option value="">— Tidak ada aset —</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_code }} — {{ $asset->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="location_id" class="form-label fw-medium">Lokasi</label>
                    <select id="location_id" name="location_id" class="form-select @error('location_id') is-invalid @enderror" data-searchable>
                        <option value="">— Pilih Lokasi —</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Deskripsi <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" rows="6"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Jelaskan masalah atau permintaan Anda secara detail..." required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" id="auto_assign" name="auto_assign" value="1" class="form-check-input" checked>
                        <label for="auto_assign" class="form-check-label">
                            Tugaskan otomatis ke agent yang tersedia
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('sd.tickets.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

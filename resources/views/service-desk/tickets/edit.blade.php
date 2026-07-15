@extends('layouts.app')

@section('title', 'Edit Tiket ' . $ticket->ticket_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.index') }}" class="text-decoration-none">Service Desk</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sd.tickets.show', $ticket) }}" class="text-decoration-none">{{ $ticket->ticket_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Tiket {{ $ticket->ticket_number }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sd.tickets.update', $ticket) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <label for="subject" class="form-label fw-medium">Judul Tiket</label>
                    <input type="text" id="subject" name="subject"
                           class="form-control @error('subject') is-invalid @enderror"
                           value="{{ old('subject', $ticket->subject) }}" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label fw-medium">Kategori</label>
                    <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror" data-searchable required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="priority" class="form-label fw-medium">Prioritas</label>
                    <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror">
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ old('priority', $ticket->priority->value) === $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="agent_id" class="form-label fw-medium">Agent</label>
                    <select id="agent_id" name="agent_id" class="form-select @error('agent_id') is-invalid @enderror" data-searchable>
                        <option value="">— Pilih Agent —</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_id', $ticket->agent_id) == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="asset_id" class="form-label fw-medium">Aset Terkait</label>
                    <select id="asset_id" name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" data-searchable>
                        <option value="">— Tidak ada —</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id', $ticket->asset_id) == $asset->id ? 'selected' : '' }}>
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
                            <option value="{{ $location->id }}" {{ old('location_id', $ticket->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Deskripsi</label>
                    <textarea id="description" name="description" rows="6"
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $ticket->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('sd.tickets.show', $ticket) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

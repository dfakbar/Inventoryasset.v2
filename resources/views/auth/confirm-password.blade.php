@extends('layouts.guest')

@section('title', 'Konfirmasi Password')

@section('card-title')
    <i class="bi bi-shield-check me-2 text-warning"></i>Konfirmasi Password
@endsection

@section('content')

    <p class="text-muted small mb-4">
        Area ini memerlukan konfirmasi password sebelum melanjutkan. Masukkan password Anda untuk melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold small">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="••••••••"
                   required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Konfirmasi
            </button>
        </div>
    </form>

@endsection

@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('card-title')
    <i class="bi bi-key me-2 text-warning"></i>Reset Password
@endsection

@section('content')

    <p class="text-muted small mb-4">
        Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mereset password.
    </p>

    @if (session('status'))
        <div class="alert alert-success mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small">
                <i class="bi bi-envelope me-1 text-muted"></i>Alamat Email
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="nama@perusahaan.com"
                   required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Kirim Link Reset
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
            </a>
        </div>
    </form>

@endsection

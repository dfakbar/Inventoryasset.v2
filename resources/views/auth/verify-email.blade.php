@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('card-title')
    <i class="bi bi-envelope-check me-2 text-success"></i>Verifikasi Email
@endsection

@section('content')

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            Link verifikasi baru telah dikirimkan ke email Anda.
        </div>
    @endif

    <p class="text-muted small mb-4">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik link yang kami kirimkan. Jika tidak menerima email, kami akan mengirimkan ulang.
    </p>

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send me-1"></i>Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>

@endsection

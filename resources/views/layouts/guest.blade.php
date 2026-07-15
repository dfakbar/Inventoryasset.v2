<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — {{ config('app.name', 'AssetMS') }}</title>

    {{-- Bootstrap 5.3 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite('resources/css/guest.css')
</head>
<body>

<div class="auth-card">

    {{-- Brand --}}
    <div class="text-center mb-4">
        <div class="auth-brand mb-1">
            <i class="bi bi-building-gear me-1"></i>Asset<span>MS</span>
        </div>
        <p class="text-white-50 small mb-0">Sistem Informasi Manajemen Aset</p>
    </div>

    {{-- Card --}}
    <div class="card">
        <div class="card-header py-3 px-4">
            <h5 class="mb-0 fw-semibold text-dark">
                @yield('card-title', '<i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Masuk ke Sistem')
            </h5>
        </div>
        <div class="card-body p-4">
            @yield('content')
        </div>
    </div>

    {{-- Footer --}}
    <p class="text-center text-white-50 small mt-4 mb-0">
        &copy; {{ date('Y') }} {{ config('app.name', 'AssetMS') }}
    </p>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
@stack('scripts')
</body>
</html>

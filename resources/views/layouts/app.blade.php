<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'AssetMS') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite('resources/css/app.css')

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ── --}}
<nav id="sidebar">
    <a class="sidebar-brand" href="{{ route('dashboard') }}">
        <i class="bi bi-building-gear fs-5"></i>
        Asset<span class="accent">MS</span>
    </a>

    <div class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        @can('asset.viewAny')
        <a href="{{ route('assets.index') }}"
           class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Manajemen Aset
        </a>
        @endcan

        @can('ticket.viewAny')
        <a href="{{ route('sd.tickets.index') }}"
           class="nav-link {{ request()->routeIs('sd.tickets.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated-fill"></i> Service Desk
        </a>
        @endcan

        @canany(['ticket.create', 'ticket.manage', 'ticket.reports'])
        <div class="nav-label mt-2">Layanan IT</div>

        @can('ticket.create')
        <a href="{{ route('sd.tickets.create') }}"
           class="nav-link {{ request()->routeIs('sd.tickets.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square-fill"></i> Buat Tiket
        </a>
        @endcan

        @can('ticket.viewAny')
        <a href="{{ route('sd.tickets.status') }}"
           class="nav-link {{ request()->routeIs('sd.tickets.status') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Status Tiket
        </a>
        @endcan

        @can('ticket.manage')
        <a href="{{ route('sd.categories.index') }}"
           class="nav-link {{ request()->routeIs('sd.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i> Kategori Tiket
        </a>
        @endcan

        @can('ticket.reports')
        <a href="{{ route('sd.sla-policies.index') }}"
           class="nav-link {{ request()->routeIs('sd.sla-policies.*') ? 'active' : '' }}">
            <i class="bi bi-clock-fill"></i> Kebijakan SLA
        </a>
        <a href="{{ route('sd.reports.index') }}"
           class="nav-link {{ request()->routeIs('sd.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill"></i> Laporan & SLA
        </a>
        @endcan
        @endcanany

        {{-- Menu tambahan berdasarkan permission --}}
        @canany(['asset.create', 'category.viewAny', 'brand.viewAny', 'vendor.viewAny', 'location.viewAny'])
        <div class="nav-label mt-2">Akses Saya</div>
        @endcanany

        @can('asset.create')
        <a href="{{ route('assets.create') }}"
           class="nav-link {{ request()->routeIs('assets.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square-fill"></i> Tambah Aset
        </a>
        @endcan

        @can('category.viewAny')
        <a href="{{ route('admin.categories.index') }}"
           class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i> Manajemen Kategori
        </a>
        @endcan

        @can('brand.viewAny')
        <a href="{{ route('admin.brands.index') }}"
           class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="bi bi-bookmark-star-fill"></i> Manajemen Merek
        </a>
        @endcan

        @can('vendor.viewAny')
        <a href="{{ route('admin.vendors.index') }}"
           class="nav-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Manajemen Vendor
        </a>
        @endcan

        @can('location.viewAny')
        <a href="{{ route('admin.locations.index') }}"
           class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i> Manajemen Lokasi
        </a>
        @endcan

        @auth
        @if(auth()->user()->isAdmin())
        <div class="nav-label mt-2">Super Admin</div>
        <a href="{{ route('admin.users.index') }}"
           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Manajemen User
        </a>
        @endif
        @endauth
    </div>

    
    <div class="sidebar-footer">
        v1.0.0 &mdash; &copy; {{ date('Y') }} AssetMS
    </div>
</nav>

<div id="sidebar-overlay"></div>

{{-- ── Main Wrapper ── --}}
<div id="main-wrapper">

    {{-- Topbar --}}
    <header id="topbar">
        <button id="sidebar-toggle" class="btn btn-sm btn-outline-secondary" title="Toggle Sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <span class="topbar-title text-truncate flex-grow-1">
            @yield('title', 'Dashboard')
        </span>

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-house"></i>
                    </a>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>

        {{-- User Dropdown --}}
        @auth
        <div class="dropdown ms-auto">
            <button class="btn btn-sm d-flex align-items-center gap-2 border-0 dropdown-toggle"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    style="background:transparent;color:#495057;">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                     style="width:30px;height:30px;font-size:.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-md-inline small fw-medium">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:220px;">
                <li>
                    <div class="dropdown-item-text px-3 py-2">
                        <div class="fw-medium small">{{ auth()->user()->name }}</div>
                        <div class="text-muted small">{{ auth()->user()->email }}</div>
                        <span class="{{ auth()->user()->role->badgeClass() }} d-inline-block mt-1">
                            <i class="bi {{ auth()->user()->isAdmin() ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>
                            {{ auth()->user()->role->label() }}
                        </span>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item small text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </header>

    {{-- Flash Messages --}}
    <div class="flash-container" aria-live="polite">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    <footer class="py-2 px-4 border-top bg-white">
        <small class="text-muted">
            &copy; {{ date('Y') }} Sistem Informasi Manajemen Aset Perusahaan
        </small>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

@vite('resources/js/app.js')

@stack('scripts')
</body>
</html>

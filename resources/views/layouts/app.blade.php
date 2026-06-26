<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'AssetMS') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-w: 250px;
            --sidebar-bg: #212529;
            --sidebar-text: #adb5bd;
            --sidebar-hover-bg: rgba(255,255,255,.08);
            --sidebar-active-bg: rgba(255,255,255,.12);
            --topbar-h: 56px;
        }

        body { background: #f1f3f5; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0; z-index: 1040;
            width: var(--sidebar-w); min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            transition: transform .25s ease;
        }
        #sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

        .sidebar-brand {
            display: flex; align-items: center; gap: .5rem;
            padding: 1rem 1.25rem;
            color: #fff; font-size: 1.15rem; font-weight: 700;
            text-decoration: none; white-space: nowrap;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand span.accent { color: #0d6efd; }

        .sidebar-nav { flex: 1; padding: .75rem 0; }

        .nav-label {
            padding: .45rem 1.25rem;
            font-size: .68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            color: #6c757d;
        }

        .sidebar-nav .nav-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .58rem 1.25rem;
            color: var(--sidebar-text); font-size: .875rem;
            border-radius: 0; white-space: nowrap;
            transition: background .15s, color .15s;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.25rem; text-align: center; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover { background: var(--sidebar-hover-bg); color: #fff; }
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active-bg); color: #fff;
            border-left: 3px solid #0d6efd;
        }

        /* ── Sidebar user info ── */
        .sidebar-user {
            padding: .75rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user .user-name {
            font-size: .85rem; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user .user-email {
            font-size: .72rem; color: #6c757d;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* ── Sidebar footer ── */
        .sidebar-footer {
            padding: .6rem 1.25rem;
            font-size: .7rem; color: #495057;
            border-top: 1px solid rgba(255,255,255,.04);
        }

        /* ── Mobile overlay ── */
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 1039;
        }
        #sidebar-overlay.show { display: block; }

        /* ── Main wrapper ── */
        #main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh; display: flex; flex-direction: column;
            transition: margin-left .25s ease;
        }
        #main-wrapper.expanded { margin-left: 0; }

        /* ── Topbar ── */
        #topbar {
            position: sticky; top: 0; z-index: 1030;
            height: var(--topbar-h); background: #fff;
            border-bottom: 1px solid #dee2e6;
            display: flex; align-items: center; padding: 0 1.25rem; gap: 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .topbar-title { font-size: .95rem; font-weight: 600; color: #212529; }

        /* ── Content ── */
        #main-content { flex: 1; padding: 1.5rem; }

        /* ── Flash messages ── */
        .flash-container {
            position: fixed;
            top: calc(var(--topbar-h) + .75rem);
            right: 1rem; z-index: 1055; width: 340px;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            #sidebar.show-mobile { transform: translateX(0); }
            #main-wrapper { margin-left: 0 !important; }
        }
        @media (min-width: 992px) {
            #sidebar-overlay { display: none !important; }
        }
    </style>

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
            <i class="bi bi-house-fill"></i> Dashboard
        </a>

        <a href="{{ route('assets.index') }}"
           class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> Manajemen Aset
        </a>

        {{-- Menu tambahan berdasarkan permission --}}
        @canany(['asset.create', 'location.viewAny'])
        <div class="nav-label mt-2">Akses Saya</div>
        @endcanany

        @can('asset.create')
        <a href="{{ route('assets.create') }}"
           class="nav-link {{ request()->routeIs('assets.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square-fill"></i> Tambah Aset
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

    {{-- User info + logout --}}
    @auth
    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:32px;height:32px;font-size:.8rem;font-weight:700;color:#fff">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <span class="{{ auth()->user()->role->badgeClass() }} w-100 d-block text-center py-1 mb-2">
            <i class="bi {{ auth()->user()->isAdmin() ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>
            {{ auth()->user()->role->label() }}
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="btn btn-sm btn-outline-secondary w-100"
                    style="color:#adb5bd;border-color:rgba(255,255,255,.15)">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
    @endauth

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
    </header>

    {{-- Flash Messages --}}
    <div class="flash-container" aria-live="polite">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{!! session('info') !!}
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const sidebar     = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('main-wrapper');
        const overlay     = document.getElementById('sidebar-overlay');
        const toggleBtn   = document.getElementById('sidebar-toggle');
        const BP          = 992;

        const isMobile = () => window.innerWidth < BP;

        toggleBtn.addEventListener('click', () => {
            if (isMobile()) {
                const open = sidebar.classList.toggle('show-mobile');
                overlay.classList.toggle('show', open);
            } else {
                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('expanded');
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('show');
        });

        // Auto-dismiss flash messages setelah 4 detik
        document.querySelectorAll('.flash-container .alert').forEach(el => {
            setTimeout(() => bootstrap.Alert.getOrCreateInstance(el)?.close(), 4000);
        });
    })();
</script>

@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('judul')@yield('judul') · @endif{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/js/app.js'])
    @stack('head')
</head>
<body>
<div class="app-shell">
    {{-- Sidebar desktop --}}
    <aside class="sidebar d-none d-lg-block" aria-label="Menu utama">
        @include('partials.menu')
    </aside>

    {{-- Sidebar mobile (offcanvas) --}}
    <div class="offcanvas offcanvas-start sidebar d-lg-none" tabindex="-1" id="menuMobile" aria-label="Menu utama">
        <div class="d-flex justify-content-end p-2">
            <button type="button" class="btn btn-ikon btn-outline-secondary" data-bs-dismiss="offcanvas" aria-label="Tutup menu"><i class="ph ph-x"></i></button>
        </div>
        @include('partials.menu')
    </div>

    <div class="app-main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2 px-3 px-md-4 py-2">
                <button class="btn btn-ikon btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuMobile" aria-label="Buka menu">
                    <i class="ph ph-list"></i>
                </button>
                <span class="d-lg-none fw-semibold small">{{ config('app.name') }}</span>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="lencana {{ auth()->user()->isAdmin() ? 'lencana-ungu' : 'lencana-biru' }} d-none d-sm-inline-flex">{{ auth()->user()->peran }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ph ph-user-circle"></i>
                            <span class="d-none d-sm-inline">{{ auth()->user()->nama }}</span>
                            <i class="ph ph-caret-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item small" href="{{ route('profile.edit') }}"><i class="ph ph-user me-2"></i>Profil saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item small"><i class="ph ph-sign-out me-2"></i>Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <main class="konten muncul">
            @include('partials.notifikasi')
            @yield('konten')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>

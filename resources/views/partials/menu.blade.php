@php
    $admin = auth()->user()->isAdmin();
    $menu = [
        ['grup' => null, 'item' => [
            ['route' => 'dashboard', 'aktif' => 'dashboard', 'ikon' => 'ph-squares-four', 'label' => 'Dashboard'],
        ]],
        ['grup' => 'Data Master', 'item' => [
            ['route' => 'supplier.index', 'aktif' => 'supplier.*', 'ikon' => 'ph-users-three', 'label' => 'Supplier / Pengumpul'],
            ['route' => 'pembeli.index', 'aktif' => 'pembeli.*', 'ikon' => 'ph-storefront', 'label' => 'Pembeli'],
            ['route' => 'jenis-plastik.index', 'aktif' => 'jenis-plastik.*', 'ikon' => 'ph-recycle', 'label' => 'Jenis Plastik & Harga', 'admin' => true],
        ]],
        ['grup' => 'Transaksi', 'item' => [
            ['route' => 'setoran.index', 'aktif' => 'setoran.*', 'ikon' => 'ph-tray-arrow-down', 'label' => 'Setoran'],
            ['route' => 'penjualan.index', 'aktif' => 'penjualan.*', 'ikon' => 'ph-truck', 'label' => 'Penjualan'],
        ]],
        ['grup' => 'Migrasi', 'item' => [
            ['route' => 'impor.index', 'aktif' => ['impor.index', 'impor.pemetaan', 'impor.pratinjau'], 'ikon' => 'ph-upload-simple', 'label' => 'Impor Data', 'admin' => true],
            ['route' => 'impor.riwayat', 'aktif' => ['impor.riwayat', 'impor.hasil'], 'ikon' => 'ph-clock-counter-clockwise', 'label' => 'Riwayat Impor', 'admin' => true],
        ]],
        ['grup' => 'Laporan', 'item' => [
            ['route' => 'laporan.setoran', 'aktif' => 'laporan.setoran*', 'ikon' => 'ph-file-text', 'label' => 'Laporan Setoran'],
            ['route' => 'laporan.penjualan', 'aktif' => 'laporan.penjualan*', 'ikon' => 'ph-chart-line-up', 'label' => 'Laporan Penjualan'],
        ]],
        ['grup' => 'Pengaturan', 'item' => [
            ['route' => 'pengguna.index', 'aktif' => 'pengguna.*', 'ikon' => 'ph-user-gear', 'label' => 'Pengguna & Hak Akses', 'admin' => true],
            ['route' => 'backup.index', 'aktif' => 'backup.*', 'ikon' => 'ph-database', 'label' => 'Backup Data', 'admin' => true],
            ['route' => 'log-aktivitas.index', 'aktif' => 'log-aktivitas.*', 'ikon' => 'ph-list-magnifying-glass', 'label' => 'Log Aktivitas', 'admin' => true],
        ]],
    ];
@endphp

<a href="{{ route('dashboard') }}" class="brand">
    <span class="brand-mark"><i class="ph-bold ph-recycle"></i></span>
    <span class="brand-teks">
        <strong>Fathoni Factory</strong>
        <span>Sistem Database Online</span>
    </span>
</a>

<nav class="pb-4">
    @foreach ($menu as $bagian)
        @php
            // Tampilkan hanya menu yang sudah tersedia dan sesuai hak akses.
            $item = collect($bagian['item'])->filter(fn ($m) => Route::has($m['route']) && ($admin || empty($m['admin'])));
        @endphp
        @continue($item->isEmpty())

        @if ($bagian['grup'])
            <div class="menu-grup">{{ $bagian['grup'] }}</div>
        @endif

        @foreach ($item as $m)
            <a href="{{ route($m['route']) }}" @class(['menu-link', 'aktif' => request()->routeIs(...(array) $m['aktif'])])>
                <i class="ph {{ $m['ikon'] }}"></i>
                <span>{{ $m['label'] }}</span>
                @if (! empty($m['admin']))
                    <span class="lencana lencana-kuning tag-admin" style="font-size:.58rem">admin</span>
                @endif
            </a>
        @endforeach
    @endforeach
</nav>

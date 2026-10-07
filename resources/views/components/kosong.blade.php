@props(['ikon' => 'ph-tray', 'pesan' => 'Belum ada data.'])

<div class="kosong">
    <i class="ph {{ $ikon }}"></i>
    <div>{{ $pesan }}</div>
    {{ $slot }}
</div>

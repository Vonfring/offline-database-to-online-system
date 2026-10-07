@props(['label', 'nilai', 'ikon' => null, 'warna' => 'biru', 'keterangan' => null])

<div {{ $attributes->merge(['class' => 'kartu kartu-isi h-100']) }}>
    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
        <span class="stat-label">{{ $label }}</span>
        @if ($ikon)
            <span class="stat-ikon lencana-{{ $warna }}"><i class="ph {{ $ikon }}"></i></span>
        @endif
    </div>
    <div class="stat-nilai">{{ $nilai }}</div>
    @if ($keterangan)
        <div class="small text-redup mt-1">{{ $keterangan }}</div>
    @endif
</div>

@php
    $warna = ['pengumpul' => 'biru', 'UMKM' => 'kuning', 'pengelola sampah' => 'hijau'][$kategori] ?? null;
@endphp
@if ($kategori)
    <span class="lencana lencana-{{ $warna ?? 'biru' }}">{{ $kategori }}</span>
@else
    <span class="text-redup">-</span>
@endif

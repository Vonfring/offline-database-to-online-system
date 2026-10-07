@php
    $daftar = [1 => 'Unggah file', 2 => 'Cocokkan kolom', 3 => 'Validasi & pratinjau', 4 => 'Selesai'];
@endphp
<ol class="langkah" aria-label="Tahapan impor">
    @foreach ($daftar as $no => $label)
        <li @class(['aktif' => $no === $aktif, 'selesai' => $no < $aktif]) @if($no === $aktif) aria-current="step" @endif>
            <span class="nomor">@if($no < $aktif)<i class="ph-bold ph-check"></i>@else{{ $no }}@endif</span>{{ $label }}
        </li>
    @endforeach
</ol>

@props(['aksi', 'konfirmasi' => 'Yakin ingin menghapus data ini?', 'label' => 'Hapus', 'ikon' => true])

<form method="POST" action="{{ $aksi }}" data-konfirmasi="{{ $konfirmasi }}" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'btn btn-hapus btn-sm']) }}>
        @if ($ikon)<i class="ph ph-trash"></i>@endif
        @if ($label)<span @class(['ms-1' => $ikon])>{{ $label }}</span>@endif
    </button>
</form>

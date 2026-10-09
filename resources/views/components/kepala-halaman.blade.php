@props(['judul', 'deskripsi' => null, 'remah' => []])

<div class="kepala-halaman d-flex flex-wrap align-items-end justify-content-between gap-3">
    <div>
        @if ($remah)
            <nav class="remah" aria-label="Navigasi">
                @foreach ($remah as $label => $url)
                    <a href="{{ $url }}">{{ $label }}</a> <span class="mx-1">/</span>
                @endforeach
            </nav>
        @endif
        <h1 class="judul-halaman">{{ $judul }}</h1>
        @if ($deskripsi)
            <p>{{ $deskripsi }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="d-flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>

@if (session('sukses'))
    <div class="notifikasi notifikasi-sukses mb-4" role="status">
        <i class="ph-bold ph-check-circle"></i>
        <div>{{ session('sukses') }}</div>
    </div>
@endif

@if (session('gagal'))
    <div class="notifikasi notifikasi-gagal mb-4" role="alert">
        <i class="ph-bold ph-warning-circle"></i>
        <div>{{ session('gagal') }}</div>
    </div>
@endif

@if (session('info'))
    <div class="notifikasi notifikasi-info mb-4" role="status">
        <i class="ph-bold ph-info"></i>
        <div>{{ session('info') }}</div>
    </div>
@endif

@if ($errors->any() && ! ($sembunyikanDaftarError ?? false))
    <div class="notifikasi notifikasi-gagal mb-4" role="alert">
        <i class="ph-bold ph-warning-circle"></i>
        <div>
            <strong>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

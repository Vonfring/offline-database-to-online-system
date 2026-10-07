@extends('layouts.app')

@section('judul', 'Impor Data')

@section('konten')
<x-kepala-halaman judul="Impor Data Offline" deskripsi="Pindahkan data dari file Excel/CSV di komputer lokal ke database online.">
    <a href="{{ route('impor.riwayat') }}" class="btn btn-outline-secondary"><i class="ph ph-clock-counter-clockwise me-1"></i>Riwayat impor</a>
</x-kepala-halaman>

@include('impor._langkah', ['aktif' => 1])

@if ($sedangBerjalan)
    <div class="notifikasi notifikasi-info mb-4 align-items-center">
        <i class="ph-bold ph-hourglass-medium"></i>
        <div class="flex-grow-1">Ada proses impor yang belum selesai: <strong>{{ $sedangBerjalan['nama_file'] }}</strong> ({{ \App\Services\Impor\DaftarImpor::label($sedangBerjalan['jenis']) }}).</div>
        <a href="{{ route(($sedangBerjalan['pemetaan_dikonfirmasi'] ?? false) ? 'impor.pratinjau' : 'impor.pemetaan') }}" class="btn btn-sm btn-dark">Lanjutkan</a>
        <form method="POST" action="{{ route('impor.batal') }}">@csrf<button class="btn btn-sm btn-outline-secondary">Batalkan</button></form>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('impor.unggah') }}" enctype="multipart/form-data" class="kartu" data-cegah-ganda>
            @csrf
            <div class="kartu-kepala"><h2>1. Pilih jenis data</h2></div>
            <div class="kartu-isi">
                <div class="row g-2 pilihan-jenis">
                    @foreach ($daftarJenis as $kunci => $def)
                        <div class="col-sm-6">
                            <input class="form-check-input" type="radio" name="jenis_data" id="jenis-{{ $kunci }}" value="{{ $kunci }}" @checked($jenisDipilih === $kunci)>
                            <label for="jenis-{{ $kunci }}">
                                <strong>{{ $def->label() }}</strong>
                                <span>{{ $def->deskripsi() }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('jenis_data')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                <h2 class="fs-6 fw-semibold mt-4 mb-3">2. Unggah file</h2>
                <div class="area-unggah" data-unggah-file>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required aria-label="Pilih file">
                    <i class="ph ph-file-arrow-up"></i>
                    <div class="fw-medium mt-2" data-nama-file data-default="Klik atau seret file ke sini">Klik atau seret file ke sini</div>
                    <div class="small text-redup">Format .xlsx, .xls, atau .csv · maksimal 5 MB · maksimal 5.000 baris</div>
                </div>
                @error('file')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex justify-content-end px-4 py-3 border-top">
                <button type="submit" class="btn btn-dark" data-teks-proses="Membaca file...">Unggah & lanjutkan <i class="ph-bold ph-arrow-right ms-1"></i></button>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="kartu mb-4">
            <div class="kartu-kepala"><h2>Template Excel</h2></div>
            <div class="kartu-isi">
                <p class="small text-redup">Gunakan template agar kolom langsung cocok. Template berisi contoh data dan lembar petunjuk.</p>
                <div class="d-grid gap-2">
                    @foreach ($daftarJenis as $kunci => $def)
                        <a href="{{ route('impor.template', $kunci) }}" class="btn btn-outline-secondary btn-sm text-start d-flex align-items-center">
                            <i class="ph ph-microsoft-excel-logo me-2"></i>{{ $def->label() }}
                            <i class="ph ph-download-simple ms-auto"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="kartu">
            <div class="kartu-kepala"><h2>Urutan yang disarankan</h2></div>
            <ol class="kartu-isi small mb-0 ps-4">
                <li>Impor <strong>supplier</strong> dan <strong>pembeli</strong> terlebih dahulu.</li>
                <li>Pastikan harga jenis plastik sudah diatur.</li>
                <li>Impor <strong>setoran</strong> dan <strong>penjualan</strong> yang memakai kode supplier/pembeli tersebut.</li>
            </ol>
        </div>
    </div>
</div>

@if ($riwayatTerakhir->isNotEmpty())
    <div class="kartu mt-4">
        <div class="kartu-kepala"><h2>Impor terakhir</h2><a href="{{ route('impor.riwayat') }}" class="small">Lihat semua</a></div>
        @include('impor._tabel-riwayat', ['log' => $riwayatTerakhir])
    </div>
@endif
@endsection

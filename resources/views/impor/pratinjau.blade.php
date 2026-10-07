@extends('layouts.app')

@section('judul', 'Pratinjau Impor')

@section('konten')
<x-kepala-halaman judul="Validasi & Pratinjau" :remah="['Impor Data' => route('impor.index')]"
    :deskripsi="'File '.$sesi['nama_file'].' · '.$definisi->label()" />

@include('impor._langkah', ['aktif' => 3])

<div class="row g-3 mb-4">
    <div class="col-sm-4"><x-stat label="Total baris" :nilai="angka($hasil->jumlahBaris())" ikon="ph-rows" warna="biru" keterangan="Baris kosong diabaikan" /></div>
    <div class="col-sm-4"><x-stat label="Siap diimpor" :nilai="angka($hasil->jumlahValid())" ikon="ph-check-circle" warna="hijau" keterangan="Baris valid" /></div>
    <div class="col-sm-4"><x-stat label="Tidak valid" :nilai="angka($hasil->jumlahTidakValid())" ikon="ph-warning-circle" warna="merah" keterangan="Tidak akan disimpan" /></div>
</div>

@if ($hasil->jumlahTidakValid() > 0)
    <div class="notifikasi notifikasi-peringatan mb-4">
        <i class="ph-bold ph-warning"></i>
        <div>
            Ada <strong>{{ angka($hasil->jumlahTidakValid()) }} baris tidak valid</strong>. Anda dapat memperbaiki file di Excel lalu <strong>mengunggah ulang</strong>,
            atau melanjutkan impor hanya untuk {{ angka($hasil->jumlahValid()) }} baris yang valid.
        </div>
    </div>
@else
    <div class="notifikasi notifikasi-sukses mb-4">
        <i class="ph-bold ph-check-circle"></i>
        <div>Semua baris valid dan siap diimpor.</div>
    </div>
@endif

<div class="kartu mb-4">
    <div class="kartu-kepala">
        <ul class="nav nav-pills gap-1 small" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm @if($hasil->jumlahTidakValid()) active @endif" data-bs-toggle="tab" data-bs-target="#tab-tidak-valid" type="button" role="tab">
                    Tidak valid <span class="lencana lencana-merah ms-1">{{ $hasil->jumlahTidakValid() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-sm @unless($hasil->jumlahTidakValid()) active @endunless" data-bs-toggle="tab" data-bs-target="#tab-valid" type="button" role="tab">
                    Valid <span class="lencana lencana-hijau ms-1">{{ $hasil->jumlahValid() }}</span>
                </button>
            </li>
        </ul>
        <span class="small text-redup">Nomor baris sesuai baris di file Excel.</span>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade @if($hasil->jumlahTidakValid()) show active @endif" id="tab-tidak-valid" role="tabpanel">
            <div class="table-responsive">
                <table class="table tabel-data">
                    <thead>
                        <tr>
                            <th>Baris</th>
                            @foreach ($definisi->kolom() as $k)<th>{{ $k['label'] }}</th>@endforeach
                            <th>Masalah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hasil->tidakValid as $nomor => $b)
                            <tr class="baris-gagal">
                                <td><span class="kode">{{ $nomor }}</span></td>
                                @foreach (array_keys($definisi->kolom()) as $kunci)
                                    <td class="small">{{ \Illuminate\Support\Str::limit((string) ($b['data'][$kunci] ?? ''), 30) ?: '—' }}</td>
                                @endforeach
                                <td class="small" style="color:var(--pastel-merah-teks);min-width:260px">
                                    <ul class="mb-0 ps-3">@foreach ($b['error'] as $e)<li>{{ $e }}</li>@endforeach</ul>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($definisi->kolom()) + 2 }}"><x-kosong ikon="ph-check-circle" pesan="Tidak ada baris yang bermasalah." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade @unless($hasil->jumlahTidakValid()) show active @endunless" id="tab-valid" role="tabpanel">
            <div class="table-responsive">
                <table class="table tabel-data">
                    <thead>
                        <tr>
                            <th>Baris</th>
                            @foreach ($definisi->kolomPratinjau() as $label)<th>{{ $label }}</th>@endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($hasil->valid, 0, 300, true) as $nomor => $b)
                            <tr>
                                <td><span class="kode">{{ $nomor }}</span></td>
                                @foreach (array_keys($definisi->kolomPratinjau()) as $kunci)
                                    <td class="small text-nowrap">{{ \Illuminate\Support\Str::limit((string) ($b['tampil'][$kunci] ?? ''), 40) ?: '—' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($definisi->kolomPratinjau()) + 1 }}"><x-kosong ikon="ph-warning-circle" pesan="Belum ada baris valid." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($hasil->jumlahValid() > 300)
                <div class="small text-redup px-3 py-2 border-top">Menampilkan 300 baris pertama dari {{ angka($hasil->jumlahValid()) }} baris valid.</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <form method="POST" action="{{ route('impor.unggah') }}" enctype="multipart/form-data" class="kartu h-100" data-cegah-ganda>
            @csrf
            <input type="hidden" name="jenis_data" value="{{ $definisi->kunci() }}">
            <div class="kartu-kepala"><h2>Unggah ulang file yang sudah diperbaiki</h2></div>
            <div class="kartu-isi">
                <div class="area-unggah py-3" data-unggah-file>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required aria-label="Pilih file perbaikan">
                    <div class="fw-medium" data-nama-file data-default="Pilih file perbaikan">Pilih file perbaikan</div>
                    <div class="small text-redup">Pemetaan kolom sebelumnya dipakai lagi bila judul kolom sama.</div>
                </div>
                <button type="submit" class="btn btn-outline-secondary mt-3" data-teks-proses="Memvalidasi ulang..."><i class="ph ph-arrow-clockwise me-1"></i>Unggah & validasi ulang</button>
            </div>
        </form>
    </div>
    <div class="col-lg-6">
        <div class="kartu h-100">
            <div class="kartu-kepala"><h2>Konfirmasi impor</h2></div>
            <div class="kartu-isi">
                <p class="small text-redup">
                    {{ angka($hasil->jumlahValid()) }} baris valid akan disimpan ke database online dengan sumber data <span class="lencana lencana-ungu">impor</span>.
                    {{ angka($hasil->jumlahTidakValid()) }} baris tidak valid akan dilewati dan dicatat di riwayat impor.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('impor.konfirmasi') }}" data-cegah-ganda
                          data-konfirmasi="Simpan {{ $hasil->jumlahValid() }} baris valid ke database?">
                        @csrf
                        <button type="submit" class="btn btn-dark" @disabled($hasil->jumlahValid() === 0) data-teks-proses="Menyimpan...">
                            <i class="ph ph-database me-1"></i>Konfirmasi Impor
                        </button>
                    </form>
                    <a href="{{ route('impor.pemetaan') }}" class="btn btn-outline-secondary">Ubah pemetaan kolom</a>
                    <form method="POST" action="{{ route('impor.batal') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

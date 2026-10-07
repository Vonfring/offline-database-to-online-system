@extends('layouts.app')

@section('judul', 'Hasil Impor')

@section('konten')
<x-kepala-halaman judul="Ringkasan Hasil Impor" :remah="['Impor Data' => route('impor.index'), 'Riwayat' => route('impor.riwayat')]"
    :deskripsi="$log->nama_file.' · '.\App\Services\Impor\DaftarImpor::label($log->jenis_data)" />

@include('impor._langkah', ['aktif' => 4])

<div class="row g-3 mb-4">
    <div class="col-sm-4"><x-stat label="Total baris" :nilai="angka($log->jumlah_baris)" ikon="ph-rows" warna="biru" /></div>
    <div class="col-sm-4"><x-stat label="Berhasil disimpan" :nilai="angka($log->jumlah_berhasil)" ikon="ph-check-circle" warna="hijau" /></div>
    <div class="col-sm-4"><x-stat label="Gagal / dilewati" :nilai="angka($log->jumlah_gagal)" ikon="ph-warning-circle" warna="merah" /></div>
</div>

<div class="kartu kartu-isi">
    <dl class="row small mb-4">
        <dt class="col-sm-3 text-redup fw-normal">Waktu impor</dt><dd class="col-sm-9">{{ tanggal_id($log->created_at, 'l, j F Y · H:i') }} WIB</dd>
        <dt class="col-sm-3 text-redup fw-normal">Dilakukan oleh</dt><dd class="col-sm-9">{{ $log->pengguna->nama }}</dd>
        <dt class="col-sm-3 text-redup fw-normal">Nama file</dt><dd class="col-sm-9">{{ $log->nama_file }}</dd>
        <dt class="col-sm-3 text-redup fw-normal">Jenis data</dt><dd class="col-sm-9 mb-0"><span class="lencana lencana-biru">{{ $log->jenis_data }}</span></dd>
    </dl>

    @php
        $rute = ['supplier' => 'supplier.index', 'pembeli' => 'pembeli.index', 'setoran' => 'setoran.index', 'penjualan' => 'penjualan.index'][$log->jenis_data] ?? null;
    @endphp
    <div class="d-flex flex-wrap gap-2">
        @if ($rute && Route::has($rute))
            <a href="{{ route($rute, in_array($log->jenis_data, ['setoran', 'penjualan']) ? ['sumber' => 'impor'] : ['urut' => 'terbaru']) }}" class="btn btn-dark">
                <i class="ph ph-list-magnifying-glass me-1"></i>Periksa data di menu terkait
            </a>
        @endif
        <a href="{{ route('impor.index') }}" class="btn btn-outline-secondary"><i class="ph ph-upload-simple me-1"></i>Impor file lain</a>
    </div>
</div>
@endsection

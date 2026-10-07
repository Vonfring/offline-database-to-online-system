@extends('layouts.app')

@section('judul', $pembeli->nama)

@section('konten')
<x-kepala-halaman :judul="$pembeli->nama" :deskripsi="$pembeli->perusahaan" :remah="['Pembeli' => route('pembeli.index')]">
    <a href="{{ route('pembeli.edit', $pembeli) }}" class="btn btn-outline-secondary"><i class="ph ph-pencil-simple me-1"></i>Ubah</a>
    <x-tombol-hapus :aksi="route('pembeli.destroy', $pembeli)" :konfirmasi="'Hapus pembeli '.$pembeli->nama.'?'" class="btn btn-hapus" />
</x-kepala-halaman>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="kartu kartu-isi h-100">
            <dl class="row mb-0 small">
                <dt class="col-5 text-redup fw-normal">Kode</dt><dd class="col-7"><span class="kode">{{ $pembeli->kode_pembeli }}</span></dd>
                <dt class="col-5 text-redup fw-normal">Perusahaan</dt><dd class="col-7">{{ $pembeli->perusahaan ?? '-' }}</dd>
                <dt class="col-5 text-redup fw-normal">No HP</dt><dd class="col-7">{{ $pembeli->no_hp ?? '-' }}</dd>
                <dt class="col-5 text-redup fw-normal">Alamat</dt><dd class="col-7">{{ $pembeli->alamat ?? '-' }}</dd>
                <dt class="col-5 text-redup fw-normal">Terdaftar</dt><dd class="col-7 mb-0">{{ tanggal_id($pembeli->created_at) }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-sm-4 col-lg"><x-stat label="Jumlah penjualan" :nilai="angka($ringkasan->jumlah)" ikon="ph-truck" warna="biru" /></div>
    <div class="col-sm-4 col-lg"><x-stat label="Total berat" :nilai="kg($ringkasan->berat)" ikon="ph-scales" warna="hijau" /></div>
    <div class="col-sm-4 col-lg"><x-stat label="Total nilai" :nilai="rupiah($ringkasan->nilai)" ikon="ph-coins" warna="kuning" :keterangan="'Terakhir: '.tanggal_id($ringkasan->terakhir)" /></div>
</div>

<div class="kartu">
    <div class="kartu-kepala"><h2>Riwayat penjualan</h2></div>
    @include('partials.tabel-riwayat-transaksi', ['riwayat' => $riwayat, 'kolomNomor' => 'no_penjualan', 'rute' => 'penjualan.show', 'pesanKosong' => 'Belum ada penjualan ke pembeli ini.'])
</div>
@endsection

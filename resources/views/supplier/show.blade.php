@extends('layouts.app')

@section('judul', $supplier->nama)

@section('konten')
<x-kepala-halaman :judul="$supplier->nama" :remah="['Supplier' => route('supplier.index')]">
    <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-outline-secondary"><i class="ph ph-pencil-simple me-1"></i>Ubah</a>
    <x-tombol-hapus :aksi="route('supplier.destroy', $supplier)" :konfirmasi="'Hapus supplier '.$supplier->nama.'?'" class="btn btn-hapus" />
</x-kepala-halaman>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="kartu kartu-isi h-100">
            <dl class="row mb-0 small">
                <dt class="col-5 text-redup fw-normal">Kode</dt><dd class="col-7"><span class="kode">{{ $supplier->kode_supplier }}</span></dd>
                <dt class="col-5 text-redup fw-normal">Kategori</dt><dd class="col-7">@include('supplier._kategori', ['kategori' => $supplier->kategori])</dd>
                <dt class="col-5 text-redup fw-normal">No HP</dt><dd class="col-7">{{ $supplier->no_hp ?? '-' }}</dd>
                <dt class="col-5 text-redup fw-normal">Alamat</dt><dd class="col-7">{{ $supplier->alamat ?? '-' }}</dd>
                <dt class="col-5 text-redup fw-normal">Terdaftar</dt><dd class="col-7 mb-0">{{ tanggal_id($supplier->created_at) }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-sm-4 col-lg"><x-stat label="Jumlah setoran" :nilai="angka($ringkasan->jumlah)" ikon="ph-tray-arrow-down" warna="biru" /></div>
    <div class="col-sm-4 col-lg"><x-stat label="Total berat" :nilai="kg($ringkasan->berat)" ikon="ph-scales" warna="hijau" /></div>
    <div class="col-sm-4 col-lg"><x-stat label="Total nilai" :nilai="rupiah($ringkasan->nilai)" ikon="ph-coins" warna="kuning" :keterangan="'Terakhir: '.tanggal_id($ringkasan->terakhir)" /></div>
</div>

<div class="kartu">
    <div class="kartu-kepala"><h2>Riwayat setoran</h2></div>
    @include('partials.tabel-riwayat-transaksi', ['riwayat' => $riwayat, 'kolomNomor' => 'no_setoran', 'rute' => 'setoran.show', 'pesanKosong' => 'Supplier ini belum pernah menyetor.'])
</div>
@endsection

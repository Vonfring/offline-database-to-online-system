@extends('layouts.app')

@section('judul', 'Dashboard')

@section('konten')
<x-kepala-halaman judul="Dashboard" :deskripsi="'Selamat datang, '.auth()->user()->nama.'. Ringkasan data per '.tanggal_id(now(), 'l, j F Y').'.'" />

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><x-stat label="Supplier terdaftar" :nilai="angka($jumlahSupplier)" ikon="ph-users-three" warna="biru" /></div>
    <div class="col-sm-6 col-xl-3"><x-stat label="Pembeli terdaftar" :nilai="angka($jumlahPembeli)" ikon="ph-storefront" warna="hijau" /></div>
    <div class="col-sm-6 col-xl-3"><x-stat label="Jenis plastik" :nilai="angka($jenisPlastik->count())" ikon="ph-recycle" warna="kuning" /></div>
    <div class="col-sm-6 col-xl-3"><x-stat label="Peran Anda" :nilai="ucfirst(auth()->user()->peran)" ikon="ph-identification-badge" warna="ungu" /></div>
</div>

<div class="kartu">
    <div class="kartu-kepala"><h2>Harga plastik yang berlaku hari ini</h2></div>
    <div class="table-responsive">
        <table class="table tabel-data">
            <thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Harga beli / kg</th><th class="text-end">Harga jual / kg</th><th>Berlaku sejak</th></tr></thead>
            <tbody>
                @forelse ($jenisPlastik as $j)
                    <tr>
                        <td><span class="kode">{{ $j->kode }}</span></td>
                        <td>{{ $j->nama }}</td>
                        <td class="text-end angka">{{ $j->hargaBerlaku ? rupiah($j->hargaBerlaku->harga_beli_per_kg) : '-' }}</td>
                        <td class="text-end angka">{{ $j->hargaBerlaku ? rupiah($j->hargaBerlaku->harga_jual_per_kg) : '-' }}</td>
                        <td>{{ tanggal_id($j->hargaBerlaku?->berlaku_mulai) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-kosong pesan="Belum ada jenis plastik." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

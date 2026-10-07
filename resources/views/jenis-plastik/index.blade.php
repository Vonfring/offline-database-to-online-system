@extends('layouts.app')

@section('judul', 'Jenis Plastik & Harga')

@section('konten')
<x-kepala-halaman judul="Jenis Plastik & Harga" deskripsi="Harga beli dipakai untuk setoran, harga jual untuk penjualan. Harga lama tetap disimpan sebagai riwayat.">
    <a href="{{ route('jenis-plastik.create') }}" class="btn btn-dark"><i class="ph ph-plus me-1"></i>Tambah jenis plastik</a>
</x-kepala-halaman>

<div class="row g-3">
    @forelse ($jenisPlastik as $j)
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('jenis-plastik.show', $j) }}" class="kartu kartu-isi d-block h-100 text-decoration-none text-reset">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="kode fs-6">{{ $j->kode }}</span>
                    <span class="small text-redup">{{ $j->harga_count }} riwayat harga</span>
                </div>
                <div class="fw-semibold mb-1">{{ $j->nama }}</div>
                <div class="small text-redup mb-3" style="min-height:2.6em">{{ \Illuminate\Support\Str::limit($j->keterangan, 80) }}</div>
                @if ($j->hargaBerlaku)
                    <div class="d-flex justify-content-between small border-top pt-3">
                        <div><div class="stat-label">Beli / {{ $j->satuan }}</div><div class="fw-semibold angka">{{ rupiah($j->hargaBerlaku->harga_beli_per_kg) }}</div></div>
                        <div class="text-end"><div class="stat-label">Jual / {{ $j->satuan }}</div><div class="fw-semibold angka">{{ rupiah($j->hargaBerlaku->harga_jual_per_kg) }}</div></div>
                    </div>
                    <div class="small text-redup mt-2">Berlaku sejak {{ tanggal_id($j->hargaBerlaku->berlaku_mulai) }}</div>
                @else
                    <div class="notifikasi notifikasi-peringatan small py-2">Belum ada harga yang berlaku hari ini.</div>
                @endif
            </a>
        </div>
    @empty
        <div class="col-12"><div class="kartu"><x-kosong ikon="ph-recycle" pesan="Belum ada jenis plastik." /></div></div>
    @endforelse
</div>
@endsection

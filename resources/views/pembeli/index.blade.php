@extends('layouts.app')

@section('judul', 'Pembeli')

@section('konten')
<x-kepala-halaman judul="Pembeli" deskripsi="Pabrik dan pedagang yang membeli plastik hasil pilahan.">
    <a href="{{ route('pembeli.create') }}" class="btn btn-dark"><i class="ph ph-plus me-1"></i>Tambah pembeli</a>
</x-kepala-halaman>

<div class="kartu">
    <form method="GET" class="kartu-kepala">
        <div class="row g-2 flex-grow-1">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text"><i class="ph ph-magnifying-glass"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, kode, perusahaan, no HP...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="urut" class="form-select" aria-label="Urutkan">
                    <option value="nama" @selected($urutan === 'nama')>Urut: nama</option>
                    <option value="kode_pembeli" @selected($urutan === 'kode_pembeli')>Urut: kode</option>
                    <option value="penjualan" @selected($urutan === 'penjualan')>Transaksi terbanyak</option>
                    <option value="terbaru" @selected($urutan === 'terbaru')>Terbaru ditambah</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit">Terapkan</button>
                @if (request()->hasAny(['q', 'urut']))
                    <a href="{{ route('pembeli.index') }}" class="btn btn-outline-secondary" title="Atur ulang"><i class="ph ph-x"></i></a>
                @endif
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table tabel-data table-hover">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>No HP</th>
                    <th class="text-end">Jml penjualan</th>
                    <th class="text-end">Total nilai</th>
                    <th>Transaksi terakhir</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembeli as $p)
                    <tr>
                        <td><span class="kode">{{ $p->kode_pembeli }}</span></td>
                        <td>
                            <a href="{{ route('pembeli.show', $p) }}" class="fw-medium text-decoration-none">{{ $p->nama }}</a>
                            @if ($p->alamat)<div class="small text-redup text-truncate" style="max-width:260px">{{ $p->alamat }}</div>@endif
                        </td>
                        <td>{{ $p->perusahaan ?? '-' }}</td>
                        <td class="text-nowrap">{{ $p->no_hp ?? '-' }}</td>
                        <td class="text-end angka">{{ angka($p->penjualan_count) }}</td>
                        <td class="text-end angka text-nowrap">{{ rupiah($p->penjualan_sum_total_nilai) }}</td>
                        <td class="text-nowrap">{{ tanggal_id($p->penjualan_max_tanggal) }}</td>
                        <td class="text-end">
                            <a href="{{ route('pembeli.edit', $p) }}" class="btn btn-outline-secondary btn-sm btn-ikon" title="Ubah"><i class="ph ph-pencil-simple"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><x-kosong ikon="ph-storefront" pesan="Tidak ada pembeli yang cocok dengan pencarian." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($pembeli->total())
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-3 border-top">
            <span class="small text-redup">Menampilkan {{ $pembeli->firstItem() }}–{{ $pembeli->lastItem() }} dari {{ $pembeli->total() }} pembeli</span>
            {{ $pembeli->links() }}
        </div>
    @endif
</div>
@endsection

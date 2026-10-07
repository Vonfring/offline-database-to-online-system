@extends('layouts.app')

@section('judul', 'Supplier / Pengumpul')

@section('konten')
<x-kepala-halaman judul="Supplier / Pengumpul" deskripsi="Pengumpul, UMKM, dan pengelola sampah yang menyetor plastik.">
    <a href="{{ route('supplier.create') }}" class="btn btn-dark"><i class="ph ph-plus me-1"></i>Tambah supplier</a>
</x-kepala-halaman>

<div class="kartu">
    <form method="GET" class="kartu-kepala">
        <div class="row g-2 flex-grow-1">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="ph ph-magnifying-glass"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, kode, no HP, alamat...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="kategori" class="form-select" aria-label="Kategori">
                    <option value="">Semua kategori</option>
                    @foreach (\App\Models\Supplier::DAFTAR_KATEGORI as $k)
                        <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ ucfirst($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="urut" class="form-select" aria-label="Urutkan">
                    <option value="nama" @selected($urutan === 'nama')>Urut: nama</option>
                    <option value="kode_supplier" @selected($urutan === 'kode_supplier')>Urut: kode</option>
                    <option value="setoran" @selected($urutan === 'setoran')>Setoran terbanyak</option>
                    <option value="terbaru" @selected($urutan === 'terbaru')>Terbaru ditambah</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit">Terapkan</button>
                @if (request()->hasAny(['q', 'kategori', 'urut']))
                    <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary" title="Atur ulang"><i class="ph ph-x"></i></a>
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
                    <th>Kategori</th>
                    <th>No HP</th>
                    <th class="text-end">Jml setoran</th>
                    <th class="text-end">Total berat</th>
                    <th>Setoran terakhir</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($supplier as $s)
                    <tr>
                        <td><span class="kode">{{ $s->kode_supplier }}</span></td>
                        <td>
                            <a href="{{ route('supplier.show', $s) }}" class="fw-medium text-decoration-none">{{ $s->nama }}</a>
                            @if ($s->alamat)<div class="small text-redup text-truncate" style="max-width:260px">{{ $s->alamat }}</div>@endif
                        </td>
                        <td>@include('supplier._kategori', ['kategori' => $s->kategori])</td>
                        <td class="text-nowrap">{{ $s->no_hp ?? '-' }}</td>
                        <td class="text-end angka">{{ angka($s->setoran_count) }}</td>
                        <td class="text-end angka text-nowrap">{{ kg($s->setoran_sum_total_berat_kg) }}</td>
                        <td class="text-nowrap">{{ tanggal_id($s->setoran_max_tanggal) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('supplier.edit', $s) }}" class="btn btn-outline-secondary btn-sm btn-ikon" title="Ubah"><i class="ph ph-pencil-simple"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><x-kosong ikon="ph-users-three" pesan="Tidak ada supplier yang cocok dengan pencarian." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($supplier->hasPages() || $supplier->total())
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-3 border-top">
            <span class="small text-redup">Menampilkan {{ $supplier->firstItem() ?? 0 }}–{{ $supplier->lastItem() ?? 0 }} dari {{ $supplier->total() }} supplier</span>
            {{ $supplier->links() }}
        </div>
    @endif
</div>
@endsection

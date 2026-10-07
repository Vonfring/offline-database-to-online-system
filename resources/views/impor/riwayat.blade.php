@extends('layouts.app')

@section('judul', 'Riwayat Impor')

@section('konten')
<x-kepala-halaman judul="Riwayat Impor" deskripsi="Catatan setiap proses migrasi data offline beserta jumlah baris yang berhasil dan gagal.">
    <a href="{{ route('impor.index') }}" class="btn btn-dark"><i class="ph ph-upload-simple me-1"></i>Impor baru</a>
</x-kepala-halaman>

<div class="kartu">
    <form method="GET" class="kartu-kepala">
        <div class="row g-2 flex-grow-1">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="ph ph-magnifying-glass"></i></span>
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama file...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="jenis" class="form-select" aria-label="Jenis data">
                    <option value="">Semua jenis</option>
                    @foreach ($daftarJenis as $kunci => $def)
                        <option value="{{ $kunci }}" @selected(request('jenis') === $kunci)>{{ $def->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2"><input type="date" name="dari" value="{{ request('dari') }}" class="form-control" aria-label="Dari tanggal" title="Dari tanggal"></div>
            <div class="col-6 col-md-2"><input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" aria-label="Sampai tanggal" title="Sampai tanggal"></div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit">Terapkan</button>
                @if (request()->hasAny(['q', 'jenis', 'dari', 'sampai']))
                    <a href="{{ route('impor.riwayat') }}" class="btn btn-outline-secondary" title="Atur ulang"><i class="ph ph-x"></i></a>
                @endif
            </div>
        </div>
    </form>

    @include('impor._tabel-riwayat', ['log' => $log])

    @if ($log->hasPages())
        <div class="px-3 py-3 border-top">{{ $log->links() }}</div>
    @endif
</div>
@endsection

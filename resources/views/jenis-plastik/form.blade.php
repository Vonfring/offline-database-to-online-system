@extends('layouts.app')

@php($ubah = $jenisPlastik->exists)

@section('judul', $ubah ? 'Ubah Jenis Plastik' : 'Tambah Jenis Plastik')

@section('konten')
<x-kepala-halaman :judul="$ubah ? 'Ubah Jenis Plastik' : 'Tambah Jenis Plastik'" :remah="['Jenis Plastik' => route('jenis-plastik.index')]" />

<div class="row">
    <div class="col-lg-8 col-xl-7">
        <form method="POST" action="{{ $ubah ? route('jenis-plastik.update', $jenisPlastik) : route('jenis-plastik.store') }}" class="kartu" data-cegah-ganda>
            @csrf
            @if ($ubah) @method('PUT') @endif

            <div class="kartu-isi">
                <div class="row">
                    <x-input class="col-md-3" nama="kode" label="Kode" :nilai="$jenisPlastik->kode" wajib maxlength="10" placeholder="PET" style="text-transform:uppercase" />
                    <x-input class="col-md-6" nama="nama" label="Nama" :nilai="$jenisPlastik->nama" wajib maxlength="100" placeholder="Polyethylene Terephthalate" />
                    <x-input class="col-md-3" nama="satuan" label="Satuan" :nilai="$jenisPlastik->satuan" wajib maxlength="10" />
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="2" maxlength="1000" class="form-control" placeholder="mis. Botol air mineral, botol minuman bening">{{ old('keterangan', $jenisPlastik->keterangan) }}</textarea>
                </div>

                @unless ($ubah)
                    <div class="border-top pt-3 mt-2">
                        <p class="small fw-semibold mb-2">Harga awal</p>
                        <div class="row">
                            @include('jenis-plastik._input-harga')
                        </div>
                    </div>
                @endunless
            </div>

            <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top">
                <a href="{{ $ubah ? route('jenis-plastik.show', $jenisPlastik) : route('jenis-plastik.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-dark" data-teks-proses="Menyimpan..."><i class="ph ph-floppy-disk me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

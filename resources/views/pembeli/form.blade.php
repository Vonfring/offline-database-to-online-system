@extends('layouts.app')

@php($ubah = $pembeli->exists)

@section('judul', $ubah ? 'Ubah Pembeli' : 'Tambah Pembeli')

@section('konten')
<x-kepala-halaman :judul="$ubah ? 'Ubah Pembeli' : 'Tambah Pembeli'" :remah="['Pembeli' => route('pembeli.index')]" />

<div class="row">
    <div class="col-lg-8 col-xl-7">
        <form method="POST" action="{{ $ubah ? route('pembeli.update', $pembeli) : route('pembeli.store') }}" class="kartu" data-cegah-ganda>
            @csrf
            @if ($ubah) @method('PUT') @endif

            <div class="kartu-isi">
                <div class="row">
                    <x-input class="col-md-4" nama="kode_pembeli" label="Kode pembeli" :nilai="$pembeli->kode_pembeli" maxlength="20"
                             :placeholder="$kodeSaran" :bantuan="$ubah ? null : 'Kosongkan untuk kode otomatis.'" />
                    <x-input class="col-md-8" nama="nama" label="Nama kontak" :nilai="$pembeli->nama" wajib maxlength="100" placeholder="mis. Hendra Wijaya" />
                </div>
                <div class="row">
                    <x-input class="col-md-7" nama="perusahaan" label="Perusahaan" :nilai="$pembeli->perusahaan" maxlength="100" placeholder="mis. PT Polimer Jaya Abadi" />
                    <x-input class="col-md-5" nama="no_hp" label="No HP" :nilai="$pembeli->no_hp" maxlength="20" inputmode="tel" placeholder="08xxxxxxxxxx" />
                </div>
                <div class="mb-1">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" maxlength="500" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $pembeli->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top">
                <a href="{{ $ubah ? route('pembeli.show', $pembeli) : route('pembeli.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-dark" data-teks-proses="Menyimpan..."><i class="ph ph-floppy-disk me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

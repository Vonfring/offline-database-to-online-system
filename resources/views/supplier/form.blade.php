@extends('layouts.app')

@php($ubah = $supplier->exists)

@section('judul', $ubah ? 'Ubah Supplier' : 'Tambah Supplier')

@section('konten')
<x-kepala-halaman :judul="$ubah ? 'Ubah Supplier' : 'Tambah Supplier'" :remah="['Supplier' => route('supplier.index')]" />

<div class="row">
    <div class="col-lg-8 col-xl-7">
        <form method="POST" action="{{ $ubah ? route('supplier.update', $supplier) : route('supplier.store') }}" class="kartu" data-cegah-ganda>
            @csrf
            @if ($ubah) @method('PUT') @endif

            <div class="kartu-isi">
                <div class="row">
                    <x-input class="col-md-4" nama="kode_supplier" label="Kode supplier" :nilai="$supplier->kode_supplier" maxlength="20"
                             :placeholder="$kodeSaran" :bantuan="$ubah ? null : 'Kosongkan untuk kode otomatis.'" />
                    <x-input class="col-md-8" nama="nama" label="Nama supplier" :nilai="$supplier->nama" wajib maxlength="100" placeholder="mis. Bank Sampah Melati" />
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select id="kategori" name="kategori" class="form-select @error('kategori') is-invalid @enderror">
                            <option value="">— Pilih kategori —</option>
                            @foreach (\App\Models\Supplier::DAFTAR_KATEGORI as $k)
                                <option value="{{ $k }}" @selected(old('kategori', $supplier->kategori) === $k)>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <x-input class="col-md-6" nama="no_hp" label="No HP" :nilai="$supplier->no_hp" maxlength="20" inputmode="tel" placeholder="08xxxxxxxxxx" />
                </div>

                <div class="mb-1">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" maxlength="500" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $supplier->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top">
                <a href="{{ $ubah ? route('supplier.show', $supplier) : route('supplier.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-dark" data-teks-proses="Menyimpan..."><i class="ph ph-floppy-disk me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

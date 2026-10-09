@extends('layouts.app')

@section('judul', 'Profil Saya')

@section('konten')
<div class="kepala-halaman">
    <h1 class="judul-halaman">Profil Saya</h1>
    <p>Perbarui nama, email, dan kata sandi akun Anda.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="kartu">
            <div class="kartu-kepala"><h2>Informasi akun</h2></div>
            <form method="POST" action="{{ route('profile.update') }}" class="kartu-isi">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label class="form-label wajib" for="nama">Nama</label>
                    <input id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pengguna->nama) }}" required maxlength="100">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label wajib" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pengguna->email) }}" required maxlength="100">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <span class="form-label d-block">Peran</span>
                    <span class="lencana {{ $pengguna->isAdmin() ? 'lencana-ungu' : 'lencana-biru' }}">{{ $pengguna->peran }}</span>
                </div>
                <button class="btn btn-dark" type="submit"><i class="ph ph-floppy-disk me-1"></i>Simpan profil</button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="kartu">
            <div class="kartu-kepala"><h2>Ganti kata sandi</h2></div>
            <form method="POST" action="{{ route('password.update') }}" class="kartu-isi">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label wajib" for="current_password">Kata sandi saat ini</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label wajib" for="password">Kata sandi baru</label>
                    <input id="password" type="password" name="password" autocomplete="new-password"
                           class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Minimal 8 karakter.</div>
                </div>
                <div class="mb-4">
                    <label class="form-label wajib" for="password_confirmation">Ulangi kata sandi baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
                </div>
                <button class="btn btn-dark" type="submit"><i class="ph ph-lock-key me-1"></i>Ganti kata sandi</button>
            </form>
        </div>
    </div>
</div>
@endsection

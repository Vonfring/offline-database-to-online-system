@extends('layouts.guest')

@section('judul', 'Masuk')

@section('konten')
<div class="kartu-masuk muncul">
    <div class="text-center mb-4">
        <span class="brand-mark mx-auto mb-3" style="width:44px;height:44px;font-size:1.4rem"><i class="ph-bold ph-recycle"></i></span>
        <h1 class="judul-serif mb-1" style="font-size:2.1rem">Sistem Database Online</h1>
        <p class="text-redup mb-0 small">PT Fathoni Factory Group &middot; Pengelolaan plastik daur ulang</p>
    </div>

    <div class="kartu kartu-isi">
        @if (session('status'))
            <div class="notifikasi notifikasi-info mb-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" data-cegah-ganda>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="form-control @error('email') is-invalid @enderror" placeholder="nama@fathoni.test">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-dark w-100" data-teks-proses="Memproses...">
                Masuk <i class="ph-bold ph-arrow-right ms-1"></i>
            </button>
        </form>
    </div>

    <p class="text-center text-redup small mt-3 mb-0">Belum punya akun? Hubungi admin untuk dibuatkan akun.</p>
</div>
@endsection

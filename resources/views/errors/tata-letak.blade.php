<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul') · {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f7f6f3; color: #2f3437; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; padding: 1rem; }
        .kotak { max-width: 440px; text-align: center; }
        .kode { font-size: 4rem; font-weight: 700; line-height: 1; color: #111; letter-spacing: -0.04em; }
        h1 { font-size: 1.15rem; margin: 0.75rem 0 0.4rem; color: #111; }
        p { color: #787774; margin: 0 0 1.5rem; line-height: 1.6; }
        a { display: inline-block; background: #111; color: #fff; padding: 0.55rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="kotak">
        <div class="kode">@yield('kode')</div>
        <h1>@yield('judul')</h1>
        <p>@yield('pesan')</p>
        <a href="{{ url('/') }}">Kembali ke beranda</a>
    </div>
</body>
</html>

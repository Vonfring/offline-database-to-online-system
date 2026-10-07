<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengeluarkan pengguna yang akunnya dinonaktifkan admin saat sesi masih berjalan.
 */
class PastikanAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->aktif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi admin.']);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi akses berdasarkan peran. Contoh: ->middleware('peran:admin').
 * Admin selalu lolos karena mewarisi semua hak akses staf.
 */
class CekPeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        $pengguna = $request->user();

        if (! $pengguna || ! $pengguna->punyaPeran(...$peran)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}

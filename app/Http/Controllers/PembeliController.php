<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembeliRequest;
use App\Models\Pembeli;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembeliController extends Controller
{
    public function index(Request $request): View
    {
        $urutan = in_array($request->urut, ['nama', 'kode_pembeli', 'terbaru', 'penjualan'], true) ? $request->urut : 'nama';

        $pembeli = Pembeli::query()
            ->cari($request->q)
            ->withCount('penjualan')
            ->withSum('penjualan', 'total_nilai')
            ->withMax('penjualan', 'tanggal')
            ->when($urutan === 'terbaru', fn ($q) => $q->latest('id'))
            ->when($urutan === 'penjualan', fn ($q) => $q->orderByDesc('penjualan_count'))
            ->when(in_array($urutan, ['nama', 'kode_pembeli']), fn ($q) => $q->orderBy($urutan))
            ->paginate(15)
            ->withQueryString();

        return view('pembeli.index', compact('pembeli', 'urutan'));
    }

    public function create(): View
    {
        return view('pembeli.form', ['pembeli' => new Pembeli, 'kodeSaran' => Pembeli::kodeBerikutnya()]);
    }

    public function store(PembeliRequest $request): RedirectResponse
    {
        $pembeli = Pembeli::create($request->validated());

        return redirect()->route('pembeli.show', $pembeli)->with('sukses', "Pembeli {$pembeli->nama} berhasil ditambahkan.");
    }

    public function show(Pembeli $pembeli): View
    {
        $riwayat = $pembeli->penjualan()
            ->with('detail.jenisPlastik')
            ->latest('tanggal')->latest('id')
            ->paginate(10)
            ->withQueryString();

        $ringkasan = $pembeli->penjualan()
            ->selectRaw('count(*) as jumlah, coalesce(sum(total_berat_kg),0) as berat, coalesce(sum(total_nilai),0) as nilai, max(tanggal) as terakhir')
            ->first();

        return view('pembeli.show', compact('pembeli', 'riwayat', 'ringkasan'));
    }

    public function edit(Pembeli $pembeli): View
    {
        return view('pembeli.form', ['pembeli' => $pembeli, 'kodeSaran' => null]);
    }

    public function update(PembeliRequest $request, Pembeli $pembeli): RedirectResponse
    {
        $data = $request->validated();
        $data['kode_pembeli'] ??= $pembeli->kode_pembeli;
        $pembeli->update($data);

        return redirect()->route('pembeli.show', $pembeli)->with('sukses', 'Data pembeli berhasil diperbarui.');
    }

    public function destroy(Pembeli $pembeli): RedirectResponse
    {
        if ($pembeli->penjualan()->exists()) {
            return back()->with('gagal', "Pembeli {$pembeli->nama} tidak dapat dihapus karena sudah memiliki riwayat penjualan.");
        }

        $pembeli->delete();

        return redirect()->route('pembeli.index')->with('sukses', "Pembeli {$pembeli->nama} berhasil dihapus.");
    }
}

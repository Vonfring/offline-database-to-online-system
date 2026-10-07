<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $urutan = in_array($request->urut, ['nama', 'kode_supplier', 'terbaru', 'setoran'], true) ? $request->urut : 'nama';

        $supplier = Supplier::query()
            ->cari($request->q)
            ->when($request->kategori, fn ($q, $kategori) => $q->where('kategori', $kategori))
            ->withCount('setoran')
            ->withSum('setoran', 'total_berat_kg')
            ->withMax('setoran', 'tanggal')
            ->when($urutan === 'terbaru', fn ($q) => $q->latest('id'))
            ->when($urutan === 'setoran', fn ($q) => $q->orderByDesc('setoran_count'))
            ->when(in_array($urutan, ['nama', 'kode_supplier']), fn ($q) => $q->orderBy($urutan))
            ->paginate(15)
            ->withQueryString();

        return view('supplier.index', compact('supplier', 'urutan'));
    }

    public function create(): View
    {
        return view('supplier.form', ['supplier' => new Supplier, 'kodeSaran' => Supplier::kodeBerikutnya()]);
    }

    public function store(SupplierRequest $request): RedirectResponse|JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        // Dipakai oleh form setoran ("tambah supplier baru" tanpa meninggalkan halaman).
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $supplier->id,
                'label' => "{$supplier->kode_supplier} — {$supplier->nama}",
            ], 201);
        }

        return redirect()->route('supplier.show', $supplier)->with('sukses', "Supplier {$supplier->nama} berhasil ditambahkan.");
    }

    public function show(Request $request, Supplier $supplier): View
    {
        $riwayat = $supplier->setoran()
            ->with('detail.jenisPlastik')
            ->latest('tanggal')->latest('id')
            ->paginate(10)
            ->withQueryString();

        $ringkasan = $supplier->setoran()
            ->selectRaw('count(*) as jumlah, coalesce(sum(total_berat_kg),0) as berat, coalesce(sum(total_nilai),0) as nilai, max(tanggal) as terakhir')
            ->first();

        return view('supplier.show', compact('supplier', 'riwayat', 'ringkasan'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('supplier.form', ['supplier' => $supplier, 'kodeSaran' => null]);
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();
        $data['kode_supplier'] ??= $supplier->kode_supplier;
        $supplier->update($data);

        return redirect()->route('supplier.show', $supplier)->with('sukses', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->setoran()->exists()) {
            return back()->with('gagal', "Supplier {$supplier->nama} tidak dapat dihapus karena sudah memiliki riwayat setoran.");
        }

        $supplier->delete();

        return redirect()->route('supplier.index')->with('sukses', "Supplier {$supplier->nama} berhasil dihapus.");
    }
}

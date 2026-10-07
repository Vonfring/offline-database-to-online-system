<?php

namespace App\Http\Controllers;

use App\Models\JenisPlastik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JenisPlastikController extends Controller
{
    public function index(): View
    {
        $jenisPlastik = JenisPlastik::query()
            ->with('hargaBerlaku')
            ->withCount('harga')
            ->orderBy('kode')
            ->get();

        return view('jenis-plastik.index', compact('jenisPlastik'));
    }

    public function create(): View
    {
        return view('jenis-plastik.form', ['jenisPlastik' => new JenisPlastik(['satuan' => 'kg'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        $jenis = JenisPlastik::create($data);

        $jenis->harga()->create([
            'harga_beli_per_kg' => $data['harga_beli_per_kg'],
            'harga_jual_per_kg' => $data['harga_jual_per_kg'],
            'berlaku_mulai' => $data['berlaku_mulai'],
        ]);

        return redirect()->route('jenis-plastik.show', $jenis)->with('sukses', "Jenis plastik {$jenis->kode} berhasil ditambahkan.");
    }

    public function show(JenisPlastik $jenisPlastik): View
    {
        $jenisPlastik->load(['harga', 'hargaBerlaku']);

        return view('jenis-plastik.show', compact('jenisPlastik'));
    }

    public function edit(JenisPlastik $jenisPlastik): View
    {
        return view('jenis-plastik.form', compact('jenisPlastik'));
    }

    public function update(Request $request, JenisPlastik $jenisPlastik): RedirectResponse
    {
        $jenisPlastik->update($this->validasi($request, $jenisPlastik));

        return redirect()->route('jenis-plastik.show', $jenisPlastik)->with('sukses', 'Jenis plastik berhasil diperbarui.');
    }

    public function destroy(JenisPlastik $jenisPlastik): RedirectResponse
    {
        if ($jenisPlastik->detailSetoran()->exists() || $jenisPlastik->detailPenjualan()->exists()) {
            return back()->with('gagal', "Jenis plastik {$jenisPlastik->kode} tidak dapat dihapus karena sudah dipakai pada transaksi.");
        }

        $jenisPlastik->delete();

        return redirect()->route('jenis-plastik.index')->with('sukses', "Jenis plastik {$jenisPlastik->kode} berhasil dihapus.");
    }

    private function validasi(Request $request, ?JenisPlastik $jenis = null): array
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->kode))]);

        $aturan = [
            'kode' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9\-]+$/', Rule::unique('jenis_plastik')->ignore($jenis)],
            'nama' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:10'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];

        // Harga awal hanya diminta saat menambah jenis plastik baru.
        if (! $jenis) {
            $aturan += [
                'harga_beli_per_kg' => ['required', 'numeric', 'min:0', 'max:9999999999'],
                'harga_jual_per_kg' => ['required', 'numeric', 'min:0', 'max:9999999999'],
                'berlaku_mulai' => ['required', 'date'],
            ];
        }

        return $request->validate($aturan, [
            'kode.regex' => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung.',
        ]);
    }
}

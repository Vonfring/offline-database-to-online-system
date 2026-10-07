<?php

namespace App\Http\Controllers;

use App\Models\HargaPlastik;
use App\Models\JenisPlastik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Riwayat harga per jenis plastik. Harga lama tetap disimpan agar transaksi
 * lama tetap memakai harga yang berlaku pada tanggalnya.
 */
class HargaPlastikController extends Controller
{
    public function store(Request $request, JenisPlastik $jenisPlastik): RedirectResponse
    {
        $data = $this->validasi($request, $jenisPlastik);

        $jenisPlastik->harga()->create($data);

        return redirect()->route('jenis-plastik.show', $jenisPlastik)
            ->with('sukses', 'Harga baru berlaku mulai '.tanggal_id($data['berlaku_mulai']).' berhasil disimpan.');
    }

    public function update(Request $request, JenisPlastik $jenisPlastik, HargaPlastik $harga): RedirectResponse
    {
        abort_unless($harga->jenis_plastik_id === $jenisPlastik->id, 404);

        $harga->update($this->validasi($request, $jenisPlastik, $harga));

        return redirect()->route('jenis-plastik.show', $jenisPlastik)->with('sukses', 'Harga berhasil diperbarui.');
    }

    public function destroy(JenisPlastik $jenisPlastik, HargaPlastik $harga): RedirectResponse
    {
        abort_unless($harga->jenis_plastik_id === $jenisPlastik->id, 404);

        if ($jenisPlastik->harga()->count() <= 1) {
            return back()->with('gagal', 'Harga terakhir tidak dapat dihapus. Setiap jenis plastik harus memiliki minimal satu harga.');
        }

        $harga->delete();

        return redirect()->route('jenis-plastik.show', $jenisPlastik)->with('sukses', 'Riwayat harga berhasil dihapus.');
    }

    private function validasi(Request $request, JenisPlastik $jenis, ?HargaPlastik $harga = null): array
    {
        return $request->validate([
            'harga_beli_per_kg' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'harga_jual_per_kg' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'berlaku_mulai' => [
                'required', 'date',
                Rule::unique('harga_plastik')->where('jenis_plastik_id', $jenis->id)->ignore($harga),
            ],
        ], [
            'berlaku_mulai.unique' => 'Sudah ada harga untuk tanggal berlaku tersebut. Ubah harga yang ada atau pilih tanggal lain.',
        ]);
    }
}

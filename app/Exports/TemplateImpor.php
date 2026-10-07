<?php

namespace App\Exports;

use App\Models\JenisPlastik;
use App\Models\Pembeli;
use App\Models\Supplier;
use App\Services\Impor\DefinisiImpor;
use App\Services\Impor\DefinisiTransaksi;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Template Excel impor: lembar "Data" (diisi), "Petunjuk", dan "Kode" (untuk transaksi).
 */
class TemplateImpor implements Export, WithMultipleSheets
{
    public function __construct(private DefinisiImpor $definisi) {}

    public function sheets(): array
    {
        $kolom = $this->definisi->kolom();

        $contoh = [];
        $jumlahContoh = max(array_map(fn ($k) => count($k['contoh']), $kolom));
        for ($i = 0; $i < $jumlahContoh; $i++) {
            $contoh[] = array_map(fn ($k) => $k['contoh'][$i] ?? null, array_values($kolom));
        }

        $judul = array_map(fn ($k) => $k['label'], array_values($kolom));

        // Kolom teks (kode, no HP) diformat teks agar angka 0 di depan tidak hilang.
        $formatTeks = [];
        foreach (array_keys($kolom) as $i => $kunci) {
            if (str_starts_with($kunci, 'kode') || str_starts_with($kunci, 'no_')) {
                $formatTeks[] = $i;
            }
        }

        $petunjuk = [['Kolom', 'Wajib?', 'Keterangan']];
        foreach ($kolom as $k) {
            $petunjuk[] = [$k['label'], $k['wajib'] ? 'Wajib' : 'Opsional', $k['keterangan']];
        }
        $petunjuk[] = ['', '', ''];
        $petunjuk[] = ['Catatan umum', '', ''];
        foreach ([
            'Isi data mulai baris ke-2 pada lembar "Data". Jangan ubah judul kolom pada baris pertama.',
            'Hapus baris contoh sebelum mengunggah, atau ganti dengan data Anda.',
            'File boleh berformat .xlsx, .xls, atau .csv (maksimal 5 MB, 5.000 baris).',
            ...$this->definisi->petunjukTambahan(),
        ] as $catatan) {
            $petunjuk[] = ['•', '', $catatan];
        }

        $sheets = [
            new LembarSederhana('Data', $judul, $contoh, $formatTeks),
            new LembarSederhana('Petunjuk', $petunjuk[0], array_slice($petunjuk, 1)),
        ];

        if ($this->definisi instanceof DefinisiTransaksi) {
            $sheets[] = new LembarSederhana('Kode', ['Jenis', 'Kode', 'Nama'], $this->daftarKode());
        }

        return $sheets;
    }

    private function daftarKode(): array
    {
        $baris = [];

        foreach (JenisPlastik::orderBy('kode')->get() as $j) {
            $baris[] = ['Jenis plastik', $j->kode, $j->nama];
        }

        $mitra = $this->definisi->kunci() === 'setoran'
            ? Supplier::orderBy('kode_supplier')->get(['kode_supplier as kode', 'nama'])
            : Pembeli::orderBy('kode_pembeli')->get(['kode_pembeli as kode', 'nama']);

        $label = $this->definisi->kunci() === 'setoran' ? 'Supplier' : 'Pembeli';
        foreach ($mitra as $m) {
            $baris[] = [$label, $m->kode, $m->nama];
        }

        return $baris;
    }
}

<?php

namespace App\Services\Impor;

use App\Models\Pengguna;

/**
 * Kerangka untuk setiap jenis data yang bisa diimpor (supplier, pembeli, setoran, penjualan).
 *
 * Alur: baris mentah → petakan kolom → validasi() → pratinjau → simpan().
 */
abstract class DefinisiImpor
{
    /** Kunci jenis data, mis. "supplier". */
    abstract public function kunci(): string;

    abstract public function label(): string;

    abstract public function deskripsi(): string;

    /**
     * Kolom tujuan. Format: kunci => [label, wajib, alias[], contoh[], keterangan].
     *
     * @return array<string, array{label:string, wajib:bool, alias:array, contoh:array, keterangan:string}>
     */
    abstract public function kolom(): array;

    /**
     * Memvalidasi seluruh baris yang sudah dipetakan.
     *
     * @param  array<int, array<string, mixed>>  $baris  nomor baris file => [kunci kolom => nilai mentah]
     */
    abstract public function validasi(array $baris): HasilValidasi;

    /** Menyimpan baris valid. Mengembalikan jumlah baris yang tersimpan. */
    abstract public function simpan(HasilValidasi $hasil, Pengguna $pengguna): int;

    /** Kolom yang ditampilkan pada tabel pratinjau: kunci => label. */
    public function kolomPratinjau(): array
    {
        return collect($this->kolom())->map(fn ($k) => $k['label'])->all();
    }

    /** Teks tambahan untuk lembar "Petunjuk" pada template. */
    public function petunjukTambahan(): array
    {
        return [];
    }

    /** Mencocokkan judul kolom file dengan kolom tujuan secara otomatis. */
    public function tebakPemetaan(array $judulFile): array
    {
        $pemetaan = [];
        $judulNormal = array_map([Normalisasi::class, 'judulKolom'], $judulFile);

        foreach ($this->kolom() as $kunci => $def) {
            $kandidat = array_map(
                [Normalisasi::class, 'judulKolom'],
                [$kunci, $def['label'], ...$def['alias']]
            );

            foreach ($judulNormal as $indeks => $judul) {
                if ($judul !== '' && in_array($judul, $kandidat, true) && ! in_array($indeks, $pemetaan, true)) {
                    $pemetaan[$kunci] = $indeks;
                    break;
                }
            }
        }

        return $pemetaan;
    }
}

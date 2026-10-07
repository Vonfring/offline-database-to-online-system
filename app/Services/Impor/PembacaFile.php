<?php

namespace App\Services\Impor;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Membaca sheet pertama file Excel/CSV menjadi judul kolom + baris data.
 */
class PembacaFile
{
    public const MAKS_BARIS = 5000;

    /**
     * @return array{judul: array<int,string>, baris: array<int, array<int, mixed>>}
     *                                                                               baris: nomor baris di file (mulai 2) => nilai sel
     */
    public function baca(string $path): array
    {
        $sheets = Excel::toArray(new class implements ToArray
        {
            public function array(array $array): void {}
        }, $path);

        $isi = $sheets[0] ?? [];

        // Lewati baris kosong di bagian atas; baris pertama yang berisi dianggap judul kolom.
        while ($isi && $this->kosong(reset($isi))) {
            array_shift($isi);
        }

        $judul = array_map(fn ($j) => trim((string) $j), array_shift($isi) ?? []);

        // Buang kolom kosong di ujung kanan.
        while ($judul && end($judul) === '') {
            array_pop($judul);
        }

        $baris = [];
        foreach (array_values($isi) as $i => $sel) {
            if ($this->kosong($sel)) {
                continue;
            }
            $baris[$i + 2] = array_slice($sel, 0, count($judul));
        }

        return ['judul' => $judul, 'baris' => $baris];
    }

    /**
     * Mengubah baris mentah menjadi [kunci kolom tujuan => nilai] sesuai pemetaan.
     */
    public function petakan(array $baris, array $pemetaan): array
    {
        $hasil = [];

        foreach ($baris as $nomor => $sel) {
            $data = [];
            foreach ($pemetaan as $kunci => $indeks) {
                $data[$kunci] = ($indeks === null || $indeks === '') ? null : ($sel[(int) $indeks] ?? null);
            }

            if (! $this->kosong($data)) {
                $hasil[$nomor] = $data;
            }
        }

        return $hasil;
    }

    private function kosong(array|false $sel): bool
    {
        if ($sel === false) {
            return true;
        }

        foreach ($sel as $nilai) {
            if ($nilai !== null && trim((string) $nilai) !== '') {
                return false;
            }
        }

        return true;
    }
}

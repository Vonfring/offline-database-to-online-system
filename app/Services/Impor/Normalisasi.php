<?php

namespace App\Services\Impor;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

/**
 * Kumpulan fungsi kecil untuk merapikan nilai sel dari file Excel/CSV.
 */
class Normalisasi
{
    public static function teks(mixed $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        if (is_float($nilai) && floor($nilai) === $nilai) {
            $nilai = (string) (int) $nilai;
        }

        $nilai = trim(preg_replace('/\s+/u', ' ', (string) $nilai));

        return $nilai === '' ? null : $nilai;
    }

    public static function kode(mixed $nilai): ?string
    {
        $teks = static::teks($nilai);

        return $teks === null ? null : strtoupper(str_replace(' ', '', $teks));
    }

    /**
     * Nomor HP: Excel sering membuang angka 0 di depan (0812… menjadi 812…).
     */
    public static function noHp(mixed $nilai): ?string
    {
        $teks = static::teks($nilai);

        if ($teks === null) {
            return null;
        }

        if (preg_match('/^8\d{8,12}$/', $teks)) {
            $teks = '0'.$teks;
        }

        return $teks;
    }

    /**
     * Mengubah "1.250,5", "1,250.5", "Rp 4.500", atau 1250.5 menjadi float.
     * Mengembalikan null bila bukan angka.
     */
    public static function angka(mixed $nilai): ?float
    {
        if (is_int($nilai) || is_float($nilai)) {
            return (float) $nilai;
        }

        $teks = static::teks($nilai);

        if ($teks === null) {
            return null;
        }

        $teks = preg_replace('/^rp\.?\s*/i', '', $teks);
        $teks = preg_replace('/\s*kg$/i', '', $teks);
        $teks = str_replace(' ', '', $teks);

        $adaTitik = str_contains($teks, '.');
        $adaKoma = str_contains($teks, ',');

        if ($adaTitik && $adaKoma) {
            // Pemisah yang muncul terakhir dianggap pemisah desimal.
            if (strrpos($teks, ',') > strrpos($teks, '.')) {
                $teks = str_replace(['.', ','], ['', '.'], $teks);
            } else {
                $teks = str_replace(',', '', $teks);
            }
        } elseif ($adaTitik && preg_match('/^-?\d{1,3}(\.\d{3})+$/', $teks)) {
            $teks = str_replace('.', '', $teks);   // 1.250 → 1250 (ribuan gaya Indonesia)
        } elseif ($adaKoma && preg_match('/^-?\d{1,3}(,\d{3})+$/', $teks)) {
            $teks = str_replace(',', '', $teks);   // 1,250 → 1250
        } elseif ($adaKoma) {
            $teks = str_replace(',', '.', $teks);  // 12,5 → 12.5
        }

        return is_numeric($teks) ? (float) $teks : null;
    }

    /**
     * Menerima tanggal Excel (angka seri), "2026-08-15", "15/08/2026", "15-08-2026", "15.08.2026".
     */
    public static function tanggal(mixed $nilai): ?Carbon
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }

        if (is_int($nilai) || is_float($nilai) || (is_string($nilai) && preg_match('/^\d{5}(\.\d+)?$/', trim($nilai)))) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $nilai))->startOfDay();
            } catch (Throwable) {
                return null;
            }
        }

        $teks = trim((string) $nilai);
        // Buang bagian jam bila ada, mis. "2026-08-15 00:00:00".
        $teks = preg_replace('/[ T]\d{1,2}:\d{2}(:\d{2})?.*$/', '', $teks);

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d', 'j/n/Y', 'j-n-Y', 'j.n.Y'] as $format) {
            $tanggal = \DateTimeImmutable::createFromFormat('!'.$format, $teks);

            // Pastikan tanggal benar-benar ada (31/02/2026 ditolak) dan formatnya utuh.
            if ($tanggal !== false && $tanggal->format($format) === $teks) {
                return Carbon::instance($tanggal);
            }
        }

        return null;
    }

    /** Menyederhanakan judul kolom agar mudah dicocokkan: "Kode Supplier" → "kode_supplier". */
    public static function judulKolom(mixed $judul): string
    {
        $teks = mb_strtolower(trim((string) $judul));
        $teks = preg_replace('/\(.*?\)/', '', $teks);
        $teks = preg_replace('/[^a-z0-9]+/', '_', $teks);

        return trim($teks, '_');
    }
}

<?php

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

if (! function_exists('rupiah')) {
    /** Format angka menjadi Rupiah, mis. 1250000 → "Rp 1.250.000". */
    function rupiah(float|int|string|null $nilai, int $desimal = 0): string
    {
        return 'Rp '.number_format((float) $nilai, $desimal, ',', '.');
    }
}

if (! function_exists('angka')) {
    /** Format angka gaya Indonesia, mis. 1250.5 → "1.250,5". */
    function angka(float|int|string|null $nilai, int $desimal = 0): string
    {
        return number_format((float) $nilai, $desimal, ',', '.');
    }
}

if (! function_exists('kg')) {
    /** Format berat, mis. 1250.5 → "1.250,50 kg". */
    function kg(float|int|string|null $nilai): string
    {
        return number_format((float) $nilai, 2, ',', '.').' kg';
    }
}

if (! function_exists('tanggal_id')) {
    /** Format tanggal Indonesia, mis. "6 Okt 2026". */
    function tanggal_id(CarbonInterface|string|null $tanggal, string $format = 'j M Y'): string
    {
        if (blank($tanggal)) {
            return '-';
        }

        return Carbon::parse($tanggal)->locale('id')->translatedFormat($format);
    }
}

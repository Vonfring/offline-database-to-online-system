<?php

namespace App\Models\Concerns;

/**
 * Membuat kode berurutan (mis. SUP-0001) bila kode tidak diisi saat menyimpan.
 */
trait PunyaKodeOtomatis
{
    public static function bootPunyaKodeOtomatis(): void
    {
        static::creating(function ($model) {
            $kolom = static::$kolomKode;

            if (blank($model->{$kolom})) {
                $model->{$kolom} = static::kodeBerikutnya();
            }
        });
    }

    public static function kodeBerikutnya(): string
    {
        $kolom = static::$kolomKode;
        $awalan = static::$awalanKode.'-';

        $terakhir = static::query()
            ->where($kolom, 'like', $awalan.'%')
            ->pluck($kolom)
            ->map(fn ($kode) => (int) substr($kode, strlen($awalan)))
            ->max() ?? 0;

        return $awalan.str_pad((string) ($terakhir + 1), 4, '0', STR_PAD_LEFT);
    }
}

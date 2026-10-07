<?php

namespace App\Models\Concerns;

use App\Models\LogAktivitas;

/**
 * Mencatat setiap penambahan, perubahan, dan penghapusan data ke tabel log_aktivitas.
 */
trait CatatAktivitas
{
    public static function bootCatatAktivitas(): void
    {
        static::created(fn ($model) => LogAktivitas::catat('tambah', $model));
        static::updated(fn ($model) => LogAktivitas::catat('ubah', $model));
        static::deleted(fn ($model) => LogAktivitas::catat('hapus', $model));
    }
}

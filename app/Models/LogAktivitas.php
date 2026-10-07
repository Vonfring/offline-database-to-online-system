<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    public const UPDATED_AT = null;

    protected $fillable = ['pengguna_id', 'aksi', 'nama_tabel', 'data_id'];

    /** Bisa dimatikan sementara, misalnya saat impor massal (sudah tercatat di log_impor). */
    public static bool $aktif = true;

    public static function catat(string $aksi, Model $model): void
    {
        if (! static::$aktif || ! Auth::check()) {
            return;
        }

        static::create([
            'pengguna_id' => Auth::id(),
            'aksi' => $aksi,
            'nama_tabel' => $model->getTable(),
            'data_id' => $model->getKey(),
        ]);
    }

    public static function tanpaPencatatan(callable $callback): mixed
    {
        $sebelumnya = static::$aktif;
        static::$aktif = false;

        try {
            return $callback();
        } finally {
            static::$aktif = $sebelumnya;
        }
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}

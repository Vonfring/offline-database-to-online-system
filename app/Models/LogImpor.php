<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogImpor extends Model
{
    protected $table = 'log_impor';

    public const UPDATED_AT = null;

    protected $fillable = ['pengguna_id', 'nama_file', 'jenis_data', 'jumlah_baris', 'jumlah_berhasil', 'jumlah_gagal'];

    protected function casts(): array
    {
        return [
            'jumlah_baris' => 'integer',
            'jumlah_berhasil' => 'integer',
            'jumlah_gagal' => 'integer',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }
}

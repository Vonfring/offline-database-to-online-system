<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use CatatAktivitas;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_penjualan', 'tanggal', 'pembeli_id', 'pengguna_id',
        'total_berat_kg', 'total_nilai', 'sumber_data', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_berat_kg' => 'decimal:2',
            'total_nilai' => 'decimal:2',
        ];
    }

    public function pembeli(): BelongsTo
    {
        return $this->belongsTo(Pembeli::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}

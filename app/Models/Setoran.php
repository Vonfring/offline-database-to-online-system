<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setoran extends Model
{
    use CatatAktivitas;

    protected $table = 'setoran';

    protected $fillable = [
        'no_setoran', 'tanggal', 'supplier_id', 'pengguna_id',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailSetoran::class);
    }
}

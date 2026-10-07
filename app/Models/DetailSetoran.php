<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailSetoran extends Model
{
    protected $table = 'detail_setoran';

    public $timestamps = false;

    protected $fillable = ['setoran_id', 'jenis_plastik_id', 'berat_kg', 'harga_per_kg', 'subtotal'];

    protected function casts(): array
    {
        return [
            'berat_kg' => 'decimal:2',
            'harga_per_kg' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class);
    }

    public function jenisPlastik(): BelongsTo
    {
        return $this->belongsTo(JenisPlastik::class);
    }
}

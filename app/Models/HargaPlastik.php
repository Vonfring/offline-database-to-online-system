<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class HargaPlastik extends Model
{
    use CatatAktivitas;

    protected $table = 'harga_plastik';

    public $timestamps = false;

    protected $fillable = ['jenis_plastik_id', 'harga_beli_per_kg', 'harga_jual_per_kg', 'berlaku_mulai'];

    protected function casts(): array
    {
        return [
            'harga_beli_per_kg' => 'decimal:2',
            'harga_jual_per_kg' => 'decimal:2',
            'berlaku_mulai' => 'date',
        ];
    }

    public function jenisPlastik(): BelongsTo
    {
        return $this->belongsTo(JenisPlastik::class);
    }

    /**
     * Harga yang berlaku pada tanggal tertentu: baris dengan berlaku_mulai terbaru
     * yang tidak melewati tanggal tersebut.
     */
    public static function berlakuPada(int $jenisPlastikId, CarbonInterface|string $tanggal): ?self
    {
        $tanggal = Carbon::parse($tanggal)->toDateString();

        return static::query()
            ->where('jenis_plastik_id', $jenisPlastikId)
            ->whereDate('berlaku_mulai', '<=', $tanggal)
            ->orderByDesc('berlaku_mulai')
            ->orderByDesc('id')
            ->first();
    }
}

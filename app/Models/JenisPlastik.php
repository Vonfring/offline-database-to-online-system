<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class JenisPlastik extends Model
{
    use CatatAktivitas;

    protected $table = 'jenis_plastik';

    public $timestamps = false;

    protected $fillable = ['kode', 'nama', 'satuan', 'keterangan'];

    public function harga(): HasMany
    {
        return $this->hasMany(HargaPlastik::class)->orderByDesc('berlaku_mulai');
    }

    /** Harga yang berlaku hari ini (berlaku_mulai terbaru yang <= hari ini). */
    public function hargaBerlaku(): HasOne
    {
        return $this->hasOne(HargaPlastik::class)->ofMany(
            ['berlaku_mulai' => 'max', 'id' => 'max'],
            fn ($q) => $q->where('berlaku_mulai', '<=', Carbon::today()->toDateString())
        );
    }

    public function hargaPada(CarbonInterface|string $tanggal): ?HargaPlastik
    {
        return HargaPlastik::berlakuPada($this->id, $tanggal);
    }

    public function detailSetoran(): HasMany
    {
        return $this->hasMany(DetailSetoran::class);
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}

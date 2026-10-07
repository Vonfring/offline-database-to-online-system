<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\PunyaKodeOtomatis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembeli extends Model
{
    use CatatAktivitas, HasFactory, PunyaKodeOtomatis;

    protected $table = 'pembeli';

    protected $fillable = ['kode_pembeli', 'nama', 'perusahaan', 'no_hp', 'alamat'];

    protected static string $kolomKode = 'kode_pembeli';

    protected static string $awalanKode = 'PBL';

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        return $query->when($kata, fn (Builder $q) => $q->where(function (Builder $q) use ($kata) {
            $q->where('nama', 'ilike', "%{$kata}%")
                ->orWhere('kode_pembeli', 'ilike', "%{$kata}%")
                ->orWhere('perusahaan', 'ilike', "%{$kata}%")
                ->orWhere('no_hp', 'ilike', "%{$kata}%");
        }));
    }
}

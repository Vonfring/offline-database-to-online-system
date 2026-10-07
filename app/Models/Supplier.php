<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use App\Models\Concerns\PunyaKodeOtomatis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use CatatAktivitas, HasFactory, PunyaKodeOtomatis;

    public const DAFTAR_KATEGORI = ['pengumpul', 'UMKM', 'pengelola sampah'];

    protected $table = 'supplier';

    protected $fillable = ['kode_supplier', 'nama', 'kategori', 'no_hp', 'alamat'];

    protected static string $kolomKode = 'kode_supplier';

    protected static string $awalanKode = 'SUP';

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        return $query->when($kata, fn (Builder $q) => $q->where(function (Builder $q) use ($kata) {
            $q->where('nama', 'ilike', "%{$kata}%")
                ->orWhere('kode_supplier', 'ilike', "%{$kata}%")
                ->orWhere('no_hp', 'ilike', "%{$kata}%")
                ->orWhere('alamat', 'ilike', "%{$kata}%");
        }));
    }
}

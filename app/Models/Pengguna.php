<?php

namespace App\Models;

use App\Models\Concerns\CatatAktivitas;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use CatatAktivitas, HasFactory, Notifiable;

    public const PERAN_ADMIN = 'admin';

    public const PERAN_STAF = 'staf';

    public const DAFTAR_PERAN = [self::PERAN_ADMIN, self::PERAN_STAF];

    protected $table = 'pengguna';

    protected $fillable = ['nama', 'email', 'password', 'peran', 'aktif'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->peran === self::PERAN_ADMIN;
    }

    /**
     * Admin mewarisi semua hak akses staf (generalisasi pada use case).
     */
    public function punyaPeran(string ...$peran): bool
    {
        return $this->isAdmin() || in_array($this->peran, $peran, true);
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function logImpor(): HasMany
    {
        return $this->hasMany(LogImpor::class);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }
}

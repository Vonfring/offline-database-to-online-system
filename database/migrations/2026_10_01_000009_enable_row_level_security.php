<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Supabase membuka skema "public" melalui REST API (PostgREST).
 * Dengan RLS aktif tanpa policy, tabel tidak bisa dibaca/diubah lewat API publik
 * (anon key), sedangkan aplikasi Laravel tetap bisa mengakses karena terhubung
 * sebagai pemilik tabel (role "postgres").
 */
return new class extends Migration
{
    private array $tabel = [
        'migrations', 'pengguna', 'supplier', 'pembeli', 'jenis_plastik', 'harga_plastik',
        'setoran', 'detail_setoran', 'penjualan', 'detail_penjualan', 'log_impor', 'log_aktivitas',
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->tabel as $tabel) {
            DB::statement("ALTER TABLE {$tabel} ENABLE ROW LEVEL SECURITY");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->tabel as $tabel) {
            DB::statement("ALTER TABLE {$tabel} DISABLE ROW LEVEL SECURITY");
        }
    }
};

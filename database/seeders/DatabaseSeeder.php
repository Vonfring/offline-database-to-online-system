<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Data contoh untuk demo. Jalankan: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            JenisPlastikSeeder::class,
            SupplierSeeder::class,
            PembeliSeeder::class,
        ]);
    }
}

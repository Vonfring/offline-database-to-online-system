<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['nama' => 'Admin Fathoni', 'email' => 'admin@fathoni.test', 'password' => 'admin12345', 'peran' => 'admin', 'aktif' => true],
            ['nama' => 'Dewi Lestari', 'email' => 'staf@fathoni.test', 'password' => 'staf12345', 'peran' => 'staf', 'aktif' => true],
            ['nama' => 'Bagas Saputra', 'email' => 'bagas@fathoni.test', 'password' => 'staf12345', 'peran' => 'staf', 'aktif' => true],
        ];

        foreach ($akun as $data) {
            Pengguna::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}

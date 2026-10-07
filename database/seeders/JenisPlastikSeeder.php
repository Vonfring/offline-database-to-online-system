<?php

namespace Database\Seeders;

use App\Models\JenisPlastik;
use Illuminate\Database\Seeder;

class JenisPlastikSeeder extends Seeder
{
    public function run(): void
    {
        // Harga contoh (Rp/kg) dengan tiga kali perubahan harga selama periode demo.
        $data = [
            [
                'kode' => 'PET', 'nama' => 'Polyethylene Terephthalate',
                'keterangan' => 'Botol air mineral dan botol minuman bening. Diterima dalam kondisi sudah dipres atau dicacah.',
                'harga' => [['2026-06-01', 4800, 7000], ['2026-08-01', 5000, 7300], ['2026-09-15', 5200, 7500]],
            ],
            [
                'kode' => 'HDPE', 'nama' => 'High-Density Polyethylene',
                'keterangan' => 'Jeriken, galon oli, botol sampo dan deterjen.',
                'harga' => [['2026-06-01', 5500, 7800], ['2026-08-01', 5600, 8000], ['2026-09-15', 5800, 8200]],
            ],
            [
                'kode' => 'LDPE', 'nama' => 'Low-Density Polyethylene',
                'keterangan' => 'Kantong kresek, plastik kemasan, dan plastik film.',
                'harga' => [['2026-06-01', 3000, 4500], ['2026-08-01', 3200, 4700], ['2026-09-15', 3100, 4600]],
            ],
            [
                'kode' => 'ABS', 'nama' => 'Acrylonitrile Butadiene Styrene',
                'keterangan' => 'Casing elektronik, mainan keras, dan komponen otomotif.',
                'harga' => [['2026-06-01', 6500, 9000], ['2026-08-01', 6800, 9400], ['2026-09-15', 7000, 9600]],
            ],
        ];

        foreach ($data as $d) {
            $jenis = JenisPlastik::updateOrCreate(
                ['kode' => $d['kode']],
                ['nama' => $d['nama'], 'satuan' => 'kg', 'keterangan' => $d['keterangan']]
            );

            foreach ($d['harga'] as [$tanggal, $beli, $jual]) {
                $jenis->harga()->updateOrCreate(
                    ['berlaku_mulai' => $tanggal],
                    ['harga_beli_per_kg' => $beli, 'harga_jual_per_kg' => $jual]
                );
            }
        }
    }
}

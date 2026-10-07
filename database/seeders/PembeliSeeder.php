<?php

namespace Database\Seeders;

use App\Models\Pembeli;
use Illuminate\Database\Seeder;

class PembeliSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Hendra Wijaya', 'PT Polimer Jaya Abadi', '081298765432', 'Kawasan Industri Jababeka II Blok C2, Cikarang'],
            ['Rina Marlina', 'CV Biji Plastik Sentosa', '082133445566', 'Jl. Raya Serang KM 12, Cikupa, Kab. Tangerang'],
            ['Agus Setiawan', 'PT Recycle Indo Pratama', '081311009988', 'Kawasan Industri MM2100 Blok J5, Cibitung'],
            ['Yohanes Kurniawan', 'PT Serat Poliester Nusantara', '081219283746', 'Jl. Raya Purwakarta KM 7, Karawang'],
            ['Siti Rahmawati', 'UD Cacahan Plastik Makmur', '085710293847', 'Jl. Raya Bogor KM 30, Cimanggis, Depok'],
            ['Budi Hartono', 'PT Kemasan Hijau Indonesia', '081377788866', 'Kawasan Industri Pulogadung, Jakarta Timur'],
            ['Lina Gunawan', 'CV Aneka Pelet Plastik', '087883726154', 'Jl. Industri Raya No. 23, Cikande, Kab. Serang'],
            ['Fajar Nugroho', 'PT Elektronik Daur Ulang', '081266554433', 'Kawasan EJIP Plot 5C, Cikarang Selatan'],
        ];

        foreach ($data as $i => [$nama, $perusahaan, $hp, $alamat]) {
            Pembeli::updateOrCreate(
                ['kode_pembeli' => sprintf('PBL-%04d', $i + 1)],
                ['nama' => $nama, 'perusahaan' => $perusahaan, 'no_hp' => $hp, 'alamat' => $alamat]
            );
        }
    }
}

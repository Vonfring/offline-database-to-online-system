<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Bank Sampah Melati Bersih', 'pengelola sampah', '081287654321', 'Jl. Melati Raya No. 12, Bekasi Timur, Kota Bekasi'],
            ['Pengepul Pak Slamet', 'pengumpul', '081311223344', 'Kp. Rawa Bambu RT 03/RW 05, Cikarang Barat, Kab. Bekasi'],
            ['CV Daur Ulang Mandiri', 'UMKM', '085711122233', 'Jl. Industri Kecil No. 8, Cibitung, Kab. Bekasi'],
            ['Bank Sampah Hijau Lestari', 'pengelola sampah', '081398765412', 'Jl. Kenanga No. 4, Tambun Selatan, Kab. Bekasi'],
            ['Pengepul Bu Siti Aminah', 'pengumpul', '087812345670', 'Jl. Raya Setu No. 45, Setu, Kab. Bekasi'],
            ['TPS3R Sejahtera Bersama', 'pengelola sampah', '081234509876', 'Jl. Pemuda No. 21, Kranji, Kota Bekasi'],
            ['UD Plastik Jaya Makmur', 'UMKM', '082112233445', 'Jl. Sultan Agung KM 28, Medan Satria, Kota Bekasi'],
            ['Pengepul Mas Joko', 'pengumpul', '081556677889', 'Gg. Mawar No. 3, Bantar Gebang, Kota Bekasi'],
            ['Bank Sampah Berkah Ibu', 'pengelola sampah', '085692837465', 'Perum Graha Asri Blok C5, Cikarang Utara'],
            ['Pengepul Haji Rohmat', 'pengumpul', '081277788899', 'Jl. Raya Narogong KM 12, Cileungsi, Kab. Bogor'],
            ['KSM Peduli Lingkungan', 'pengelola sampah', '089611223344', 'Jl. Cut Mutia No. 17, Margahayu, Kota Bekasi'],
            ['CV Sumber Rejeki Plastik', 'UMKM', '081908070605', 'Kawasan Pergudangan Tambun Blok A3, Kab. Bekasi'],
            ['Pengepul Bang Udin', 'pengumpul', '085244556677', 'Jl. Pondok Ungu Permai No. 9, Babelan, Kab. Bekasi'],
            ['Bank Sampah Sekolah Cendekia', 'pengelola sampah', '081345671234', 'Jl. Pendidikan No. 1, Jatiasih, Kota Bekasi'],
            ['UD Barokah Rongsok', 'UMKM', '087766554433', 'Jl. Raya Hankam No. 66, Pondok Gede, Kota Bekasi'],
        ];

        foreach ($data as $i => [$nama, $kategori, $hp, $alamat]) {
            Supplier::updateOrCreate(
                ['kode_supplier' => sprintf('SUP-%04d', $i + 1)],
                ['nama' => $nama, 'kategori' => $kategori, 'no_hp' => $hp, 'alamat' => $alamat]
            );
        }
    }
}

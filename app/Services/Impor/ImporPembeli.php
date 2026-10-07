<?php

namespace App\Services\Impor;

use App\Models\Pembeli;

class ImporPembeli extends DefinisiMaster
{
    public function kunci(): string
    {
        return 'pembeli';
    }

    public function label(): string
    {
        return 'Pembeli';
    }

    public function deskripsi(): string
    {
        return 'Daftar pabrik atau pedagang yang membeli plastik hasil pilahan.';
    }

    protected function model(): string
    {
        return Pembeli::class;
    }

    protected function kolomKode(): string
    {
        return 'kode_pembeli';
    }

    public function kolom(): array
    {
        return [
            'kode_pembeli' => ['label' => 'Kode Pembeli', 'wajib' => false, 'alias' => ['kode', 'id_pembeli', 'no_pembeli'], 'contoh' => ['PBL-0101', 'PBL-0102'], 'keterangan' => 'Opsional. Kosongkan agar dibuat otomatis (PBL-0001, dst.).'],
            'nama' => ['label' => 'Nama', 'wajib' => true, 'alias' => ['nama_pembeli', 'pembeli', 'nama_kontak'], 'contoh' => ['Hendra Wijaya', 'Rina Marlina'], 'keterangan' => 'Wajib. Nama kontak pembeli. Tidak boleh sama dengan data yang sudah ada.'],
            'perusahaan' => ['label' => 'Perusahaan', 'wajib' => false, 'alias' => ['nama_perusahaan', 'pt', 'cv'], 'contoh' => ['PT Polimer Jaya Abadi', 'CV Biji Plastik Sentosa'], 'keterangan' => 'Opsional. Maksimal 100 karakter.'],
            'no_hp' => ['label' => 'No HP', 'wajib' => false, 'alias' => ['hp', 'telepon', 'no_telp', 'nomor_hp', 'no_telepon'], 'contoh' => ['081298765432', '021-5551234'], 'keterangan' => 'Opsional. Angka, spasi, + atau -. Maksimal 20 karakter.'],
            'alamat' => ['label' => 'Alamat', 'wajib' => false, 'alias' => ['alamat_lengkap'], 'contoh' => ['Kawasan Industri Jababeka Blok C2, Cikarang', 'Jl. Raya Serang KM 12, Tangerang'], 'keterangan' => 'Opsional.'],
        ];
    }

    protected function rapikan(array $mentah): array
    {
        return [
            'kode_pembeli' => Normalisasi::kode($mentah['kode_pembeli'] ?? null),
            'nama' => Normalisasi::teks($mentah['nama'] ?? null),
            'perusahaan' => Normalisasi::teks($mentah['perusahaan'] ?? null),
            'no_hp' => Normalisasi::noHp($mentah['no_hp'] ?? null),
            'alamat' => Normalisasi::teks($mentah['alamat'] ?? null),
        ];
    }

    protected function aturan(): array
    {
        return [
            'kode_pembeli' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/'],
            'nama' => ['required', 'string', 'max:100'],
            'perusahaan' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }
}

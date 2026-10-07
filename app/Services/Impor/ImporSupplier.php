<?php

namespace App\Services\Impor;

use App\Models\Supplier;
use Illuminate\Validation\Rule;

class ImporSupplier extends DefinisiMaster
{
    public function kunci(): string
    {
        return 'supplier';
    }

    public function label(): string
    {
        return 'Supplier / Pengumpul';
    }

    public function deskripsi(): string
    {
        return 'Daftar pengumpul, UMKM, dan pengelola sampah yang menyetor plastik.';
    }

    protected function model(): string
    {
        return Supplier::class;
    }

    protected function kolomKode(): string
    {
        return 'kode_supplier';
    }

    public function kolom(): array
    {
        return [
            'kode_supplier' => ['label' => 'Kode Supplier', 'wajib' => false, 'alias' => ['kode', 'id_supplier', 'no_supplier'], 'contoh' => ['SUP-0101', 'SUP-0102'], 'keterangan' => 'Opsional. Kosongkan agar dibuat otomatis (SUP-0001, dst.).'],
            'nama' => ['label' => 'Nama', 'wajib' => true, 'alias' => ['nama_supplier', 'nama_pengumpul', 'supplier'], 'contoh' => ['Bank Sampah Melati', 'Pengepul Bu Siti'], 'keterangan' => 'Wajib. Maksimal 100 karakter. Tidak boleh sama dengan data yang sudah ada.'],
            'kategori' => ['label' => 'Kategori', 'wajib' => false, 'alias' => ['jenis', 'jenis_supplier', 'tipe'], 'contoh' => ['pengelola sampah', 'pengumpul'], 'keterangan' => 'Opsional. Isi salah satu: pengumpul, UMKM, pengelola sampah.'],
            'no_hp' => ['label' => 'No HP', 'wajib' => false, 'alias' => ['hp', 'telepon', 'no_telp', 'nomor_hp', 'no_telepon'], 'contoh' => ['081234567890', '085712345678'], 'keterangan' => 'Opsional. Angka, spasi, + atau -. Maksimal 20 karakter.'],
            'alamat' => ['label' => 'Alamat', 'wajib' => false, 'alias' => ['alamat_lengkap'], 'contoh' => ['Jl. Melati No. 5, Bekasi', 'Kp. Rawa Bambu RT 03, Tangerang'], 'keterangan' => 'Opsional.'],
        ];
    }

    protected function rapikan(array $mentah): array
    {
        $kategori = Normalisasi::teks($mentah['kategori'] ?? null);

        // Samakan penulisan kategori, mis. "umkm" → "UMKM".
        if ($kategori !== null) {
            $cocok = collect(Supplier::DAFTAR_KATEGORI)->first(fn ($k) => mb_strtolower($k) === mb_strtolower($kategori));
            $kategori = $cocok ?? $kategori;
        }

        return [
            'kode_supplier' => Normalisasi::kode($mentah['kode_supplier'] ?? null),
            'nama' => Normalisasi::teks($mentah['nama'] ?? null),
            'kategori' => $kategori,
            'no_hp' => Normalisasi::noHp($mentah['no_hp'] ?? null),
            'alamat' => Normalisasi::teks($mentah['alamat'] ?? null),
        ];
    }

    protected function aturan(): array
    {
        return [
            'kode_supplier' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/'],
            'nama' => ['required', 'string', 'max:100'],
            'kategori' => ['nullable', Rule::in(Supplier::DAFTAR_KATEGORI)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }
}

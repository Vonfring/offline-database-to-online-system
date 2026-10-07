<?php

namespace App\Services\Impor;

use App\Models\Pembeli;
use App\Services\LayananTransaksi;

class ImporPenjualan extends DefinisiTransaksi
{
    public function kunci(): string
    {
        return 'penjualan';
    }

    public function label(): string
    {
        return 'Penjualan';
    }

    public function deskripsi(): string
    {
        return 'Catatan penjualan plastik hasil pilahan ke pembeli.';
    }

    protected function jenisTransaksi(): string
    {
        return LayananTransaksi::PENJUALAN;
    }

    protected function kolomKodeMitra(): string
    {
        return 'kode_pembeli';
    }

    protected function namaMitra(): string
    {
        return 'pembeli';
    }

    protected function namaHarga(): string
    {
        return 'harga jual';
    }

    protected function petaMitra(): array
    {
        return Pembeli::query()->pluck('id', 'kode_pembeli')->mapWithKeys(fn ($id, $kode) => [strtoupper($kode) => $id])->all();
    }

    public function kolom(): array
    {
        return [
            'no_penjualan' => ['label' => 'No Penjualan', 'wajib' => true, 'alias' => ['nomor', 'no', 'nomor_penjualan', 'no_faktur', 'no_invoice', 'no_transaksi'], 'contoh' => ['JL-2025-001', 'JL-2025-001'], 'keterangan' => 'Wajib. Nomor faktur dari catatan offline. Baris dengan nomor sama digabung menjadi satu penjualan.'],
            'tanggal' => ['label' => 'Tanggal', 'wajib' => true, 'alias' => ['tgl', 'tanggal_penjualan', 'tanggal_jual'], 'contoh' => ['2026-06-20', '2026-06-20'], 'keterangan' => 'Wajib. Format 2026-06-20 atau 20/06/2026. Tidak boleh melebihi hari ini.'],
            'kode_pembeli' => ['label' => 'Kode Pembeli', 'wajib' => true, 'alias' => ['pembeli', 'id_pembeli'], 'contoh' => ['PBL-0001', 'PBL-0001'], 'keterangan' => 'Wajib. Harus sudah terdaftar di menu Pembeli.'],
            'kode_plastik' => ['label' => 'Kode Plastik', 'wajib' => true, 'alias' => ['jenis_plastik', 'plastik', 'jenis', 'kode_jenis'], 'contoh' => ['PET', 'ABS'], 'keterangan' => 'Wajib. PET, HDPE, LDPE, atau ABS.'],
            'berat_kg' => ['label' => 'Berat (kg)', 'wajib' => true, 'alias' => ['berat', 'kg', 'berat_kg', 'tonase'], 'contoh' => [1500, 420.75], 'keterangan' => 'Wajib. Angka lebih dari 0. Desimal boleh memakai koma atau titik.'],
            'harga_per_kg' => ['label' => 'Harga per kg', 'wajib' => false, 'alias' => ['harga', 'harga_kg', 'harga_jual', 'harga_jual_per_kg'], 'contoh' => [null, 9500], 'keterangan' => 'Opsional. Kosongkan untuk memakai harga jual yang berlaku pada tanggal penjualan.'],
        ];
    }
}

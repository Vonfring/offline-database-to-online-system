<?php

namespace App\Services\Impor;

use App\Models\Supplier;
use App\Services\LayananTransaksi;

class ImporSetoran extends DefinisiTransaksi
{
    public function kunci(): string
    {
        return 'setoran';
    }

    public function label(): string
    {
        return 'Setoran (Penerimaan)';
    }

    public function deskripsi(): string
    {
        return 'Catatan timbang plastik yang diterima dari supplier.';
    }

    protected function jenisTransaksi(): string
    {
        return LayananTransaksi::SETORAN;
    }

    protected function kolomKodeMitra(): string
    {
        return 'kode_supplier';
    }

    protected function namaMitra(): string
    {
        return 'supplier';
    }

    protected function namaHarga(): string
    {
        return 'harga beli';
    }

    protected function petaMitra(): array
    {
        return Supplier::query()->pluck('id', 'kode_supplier')->mapWithKeys(fn ($id, $kode) => [strtoupper($kode) => $id])->all();
    }

    public function kolom(): array
    {
        return [
            'no_setoran' => ['label' => 'No Setoran', 'wajib' => true, 'alias' => ['nomor', 'no', 'nomor_setoran', 'no_nota', 'no_transaksi'], 'contoh' => ['ST-2025-001', 'ST-2025-001'], 'keterangan' => 'Wajib. Nomor nota dari catatan offline. Baris dengan nomor sama digabung menjadi satu setoran.'],
            'tanggal' => ['label' => 'Tanggal', 'wajib' => true, 'alias' => ['tgl', 'tanggal_setoran', 'tanggal_timbang'], 'contoh' => ['2026-06-15', '2026-06-15'], 'keterangan' => 'Wajib. Format 2026-06-15 atau 15/06/2026. Tidak boleh melebihi hari ini.'],
            'kode_supplier' => ['label' => 'Kode Supplier', 'wajib' => true, 'alias' => ['supplier', 'id_supplier'], 'contoh' => ['SUP-0001', 'SUP-0001'], 'keterangan' => 'Wajib. Harus sudah terdaftar di menu Supplier.'],
            'kode_plastik' => ['label' => 'Kode Plastik', 'wajib' => true, 'alias' => ['jenis_plastik', 'plastik', 'jenis', 'kode_jenis'], 'contoh' => ['PET', 'HDPE'], 'keterangan' => 'Wajib. PET, HDPE, LDPE, atau ABS.'],
            'berat_kg' => ['label' => 'Berat (kg)', 'wajib' => true, 'alias' => ['berat', 'kg', 'berat_kg', 'tonase'], 'contoh' => [125.5, 80], 'keterangan' => 'Wajib. Angka lebih dari 0. Desimal boleh memakai koma atau titik.'],
            'harga_per_kg' => ['label' => 'Harga per kg', 'wajib' => false, 'alias' => ['harga', 'harga_kg', 'harga_beli', 'harga_beli_per_kg'], 'contoh' => [5200, null], 'keterangan' => 'Opsional. Kosongkan untuk memakai harga beli yang berlaku pada tanggal setoran.'],
        ];
    }
}

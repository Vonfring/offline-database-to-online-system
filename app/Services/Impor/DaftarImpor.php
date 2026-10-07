<?php

namespace App\Services\Impor;

/**
 * Daftar jenis data yang dapat diimpor.
 */
class DaftarImpor
{
    /** @var array<string, class-string<DefinisiImpor>> */
    private const DEFINISI = [
        'supplier' => ImporSupplier::class,
        'pembeli' => ImporPembeli::class,
        'setoran' => ImporSetoran::class,
        'penjualan' => ImporPenjualan::class,
    ];

    public static function kunci(): array
    {
        return array_keys(self::DEFINISI);
    }

    public static function ambil(string $kunci): DefinisiImpor
    {
        abort_unless(isset(self::DEFINISI[$kunci]), 404, 'Jenis data impor tidak dikenal.');

        return app(self::DEFINISI[$kunci]);
    }

    /** @return array<string, DefinisiImpor> */
    public static function semua(): array
    {
        return array_map(fn ($kelas) => app($kelas), self::DEFINISI);
    }

    public static function label(string $kunci): string
    {
        return isset(self::DEFINISI[$kunci]) ? self::ambil($kunci)->label() : ucfirst($kunci);
    }
}

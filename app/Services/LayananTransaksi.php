<?php

namespace App\Services;

use App\Models\HargaPlastik;
use App\Models\Penjualan;
use App\Models\Setoran;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Logika bersama untuk transaksi setoran (beli dari supplier) dan penjualan (jual ke pembeli):
 * pengambilan harga berlaku, perhitungan subtotal/total, dan penyimpanan header + rincian.
 */
class LayananTransaksi
{
    public const SETORAN = 'setoran';

    public const PENJUALAN = 'penjualan';

    /** Konfigurasi tiap jenis transaksi. */
    public static function konfigurasi(string $jenis): array
    {
        return match ($jenis) {
            self::SETORAN => [
                'model' => Setoran::class,
                'kolom_nomor' => 'no_setoran',
                'kolom_mitra' => 'supplier_id',
                'kolom_harga' => 'harga_beli_per_kg',
                'awalan' => 'STR',
            ],
            self::PENJUALAN => [
                'model' => Penjualan::class,
                'kolom_nomor' => 'no_penjualan',
                'kolom_mitra' => 'pembeli_id',
                'kolom_harga' => 'harga_jual_per_kg',
                'awalan' => 'PJL',
            ],
            default => throw new InvalidArgumentException("Jenis transaksi tidak dikenal: {$jenis}"),
        };
    }

    /**
     * Harga per kg yang berlaku pada tanggal transaksi.
     * Setoran memakai harga beli, penjualan memakai harga jual.
     */
    public static function hargaBerlaku(string $jenis, int $jenisPlastikId, CarbonInterface|string $tanggal): ?float
    {
        $harga = HargaPlastik::berlakuPada($jenisPlastikId, $tanggal);

        return $harga ? (float) $harga->{static::konfigurasi($jenis)['kolom_harga']} : null;
    }

    public static function subtotal(float $beratKg, float $hargaPerKg): float
    {
        return round($beratKg * $hargaPerKg, 2);
    }

    /** Nomor transaksi berikutnya, mis. STR-202610-0007. */
    public static function nomorBerikutnya(string $jenis, CarbonInterface|string|null $tanggal = null): string
    {
        $konfig = static::konfigurasi($jenis);
        $awalan = $konfig['awalan'].'-'.Carbon::parse($tanggal ?? now())->format('Ym').'-';

        $terakhir = $konfig['model']::query()
            ->where($konfig['kolom_nomor'], 'like', $awalan.'%')
            ->pluck($konfig['kolom_nomor'])
            ->map(fn ($no) => (int) substr($no, strlen($awalan)))
            ->max() ?? 0;

        return $awalan.str_pad((string) ($terakhir + 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menyimpan transaksi beserta rinciannya. Total dihitung ulang di server.
     *
     * @param  array  $header  [nomor?, tanggal, mitra_id, pengguna_id, sumber_data, catatan]
     * @param  array<int, array{jenis_plastik_id:int, berat_kg:float, harga_per_kg:float}>  $rincian
     */
    public function simpan(string $jenis, array $header, array $rincian, ?Model $transaksi = null): Model
    {
        $konfig = static::konfigurasi($jenis);

        return DB::transaction(function () use ($jenis, $konfig, $header, $rincian, $transaksi) {
            $baris = collect($rincian)->map(fn ($r) => [
                'jenis_plastik_id' => (int) $r['jenis_plastik_id'],
                'berat_kg' => round((float) $r['berat_kg'], 2),
                'harga_per_kg' => round((float) $r['harga_per_kg'], 2),
                'subtotal' => static::subtotal(round((float) $r['berat_kg'], 2), round((float) $r['harga_per_kg'], 2)),
            ]);

            $data = [
                'tanggal' => Carbon::parse($header['tanggal'])->toDateString(),
                $konfig['kolom_mitra'] => $header['mitra_id'],
                'total_berat_kg' => round($baris->sum('berat_kg'), 2),
                'total_nilai' => round($baris->sum('subtotal'), 2),
                'catatan' => $header['catatan'] ?? null,
            ];

            if ($transaksi) {
                $transaksi->update($data);
                $transaksi->detail()->delete();
            } else {
                $transaksi = $konfig['model']::create($data + [
                    $konfig['kolom_nomor'] => $header['nomor'] ?? static::nomorBerikutnya($jenis, $header['tanggal']),
                    'pengguna_id' => $header['pengguna_id'],
                    'sumber_data' => $header['sumber_data'] ?? 'input',
                ]);
            }

            $transaksi->detail()->createMany($baris->all());

            return $transaksi->load('detail');
        });
    }
}

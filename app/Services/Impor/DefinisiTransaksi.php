<?php

namespace App\Services\Impor;

use App\Models\JenisPlastik;
use App\Models\LogAktivitas;
use App\Models\Pengguna;
use App\Services\LayananTransaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dasar impor transaksi (setoran & penjualan).
 *
 * Satu baris file = satu jenis plastik. Baris dengan nomor transaksi yang sama digabung
 * menjadi satu transaksi. Bila salah satu baris dalam satu transaksi tidak valid,
 * seluruh transaksi tersebut tidak diimpor agar total tidak terpotong.
 */
abstract class DefinisiTransaksi extends DefinisiImpor
{
    /** setoran / penjualan */
    abstract protected function jenisTransaksi(): string;

    /** Kolom kode mitra pada file, mis. "kode_supplier". */
    abstract protected function kolomKodeMitra(): string;

    /** Peta kode mitra (huruf besar) => id. */
    abstract protected function petaMitra(): array;

    abstract protected function namaMitra(): string;

    abstract protected function namaHarga(): string;

    protected function kolomNomor(): string
    {
        return LayananTransaksi::konfigurasi($this->jenisTransaksi())['kolom_nomor'];
    }

    public function kolomPratinjau(): array
    {
        return [
            $this->kolomNomor() => $this->kolom()[$this->kolomNomor()]['label'],
            'tanggal' => 'Tanggal',
            $this->kolomKodeMitra() => $this->kolom()[$this->kolomKodeMitra()]['label'],
            'kode_plastik' => 'Jenis Plastik',
            'berat_kg' => 'Berat (kg)',
            'harga_per_kg' => 'Harga/kg',
            'subtotal' => 'Subtotal',
        ];
    }

    public function petunjukTambahan(): array
    {
        return [
            'Satu baris = satu jenis plastik. Untuk transaksi dengan beberapa jenis plastik, tulis beberapa baris dengan nomor yang sama.',
            'Baris dengan nomor yang sama harus memiliki tanggal dan '.$this->namaMitra().' yang sama.',
            'Jika harga/kg dikosongkan, sistem memakai '.$this->namaHarga().' yang berlaku pada tanggal transaksi.',
            'Jika ada satu baris yang tidak valid, seluruh baris dengan nomor transaksi tersebut tidak diimpor.',
            'Kode '.$this->namaMitra().' dan kode jenis plastik harus sudah terdaftar di sistem (lihat lembar "Kode").',
        ];
    }

    public function validasi(array $baris): HasilValidasi
    {
        $hasil = new HasilValidasi;
        $kolomNomor = $this->kolomNomor();
        $kolomMitra = $this->kolomKodeMitra();
        $jenis = $this->jenisTransaksi();
        $model = LayananTransaksi::konfigurasi($jenis)['model'];

        $petaMitra = $this->petaMitra();
        $petaPlastik = JenisPlastik::query()->pluck('id', 'kode')->mapWithKeys(fn ($id, $kode) => [strtoupper($kode) => $id])->all();
        $nomorDb = $model::query()->pluck($kolomNomor)->map(fn ($n) => strtoupper($n))->flip();
        $hariIni = Carbon::today();
        $cacheHarga = [];

        // 1) Validasi tiap baris.
        $dataBaris = [];
        $errorBaris = [];

        foreach ($baris as $nomorBaris => $mentah) {
            $error = [];
            $nomor = Normalisasi::kode($mentah[$kolomNomor] ?? null);
            $tanggal = Normalisasi::tanggal($mentah['tanggal'] ?? null);
            $kodeMitra = Normalisasi::kode($mentah[$kolomMitra] ?? null);
            $kodePlastik = Normalisasi::kode($mentah['kode_plastik'] ?? null);
            $berat = Normalisasi::angka($mentah['berat_kg'] ?? null);
            $hargaMentah = Normalisasi::teks($mentah['harga_per_kg'] ?? null);
            $harga = Normalisasi::angka($mentah['harga_per_kg'] ?? null);

            if ($nomor === null) {
                $error[] = 'Nomor transaksi wajib diisi.';
            } elseif (mb_strlen($nomor) > 30) {
                $error[] = 'Nomor transaksi maksimal 30 karakter.';
            } elseif (isset($nomorDb[$nomor])) {
                $error[] = "Data ganda: nomor {$nomor} sudah ada di database.";
            }

            if (blank($mentah['tanggal'] ?? null)) {
                $error[] = 'Tanggal wajib diisi.';
            } elseif ($tanggal === null) {
                $error[] = 'Format tanggal tidak dikenali. Gunakan format 2026-08-15 atau 15/08/2026.';
            } elseif ($tanggal->greaterThan($hariIni)) {
                $error[] = 'Tanggal tidak boleh melebihi hari ini.';
            }

            $mitraId = null;
            if ($kodeMitra === null) {
                $error[] = 'Kode '.$this->namaMitra().' wajib diisi.';
            } elseif (! isset($petaMitra[$kodeMitra])) {
                $error[] = 'Kode '.$this->namaMitra()." {$kodeMitra} tidak terdaftar.";
            } else {
                $mitraId = $petaMitra[$kodeMitra];
            }

            $plastikId = null;
            if ($kodePlastik === null) {
                $error[] = 'Kode jenis plastik wajib diisi.';
            } elseif (! isset($petaPlastik[$kodePlastik])) {
                $error[] = "Kode jenis plastik {$kodePlastik} tidak terdaftar.";
            } else {
                $plastikId = $petaPlastik[$kodePlastik];
            }

            if (blank($mentah['berat_kg'] ?? null)) {
                $error[] = 'Berat (kg) wajib diisi.';
            } elseif ($berat === null) {
                $error[] = 'Berat (kg) harus berupa angka.';
            } elseif ($berat <= 0) {
                $error[] = 'Berat (kg) harus lebih dari 0.';
            } elseif ($berat > 1000000) {
                $error[] = 'Berat (kg) terlalu besar (maksimal 1.000.000 kg per baris).';
            }

            if ($hargaMentah !== null && $harga === null) {
                $error[] = 'Harga/kg harus berupa angka.';
            } elseif ($harga !== null && $harga < 0) {
                $error[] = 'Harga/kg tidak boleh negatif.';
            } elseif ($harga === null && $plastikId && $tanggal && ! $tanggal->greaterThan($hariIni)) {
                $kunci = $plastikId.'|'.$tanggal->toDateString();
                $harga = $cacheHarga[$kunci] ??= LayananTransaksi::hargaBerlaku($jenis, $plastikId, $tanggal);

                if ($harga === null) {
                    $error[] = "Belum ada {$this->namaHarga()} {$kodePlastik} yang berlaku pada ".tanggal_id($tanggal).'.';
                }
            }

            $dataBaris[$nomorBaris] = compact('nomor', 'tanggal', 'kodeMitra', 'mitraId', 'kodePlastik', 'plastikId', 'berat', 'harga');
            $errorBaris[$nomorBaris] = $error;
        }

        // 2) Validasi per kelompok nomor transaksi.
        $kelompok = collect($dataBaris)->filter(fn ($d) => $d['nomor'] !== null)->groupBy('nomor', preserveKeys: true);

        foreach ($kelompok as $nomor => $anggota) {
            $pertama = $anggota->keys()->first();
            $acuan = $anggota->first();
            $plastikTerpakai = [];

            foreach ($anggota as $nomorBaris => $d) {
                if ($nomorBaris !== $pertama) {
                    if ($d['tanggal'] && $acuan['tanggal'] && ! $d['tanggal']->equalTo($acuan['tanggal'])) {
                        $errorBaris[$nomorBaris][] = "Tanggal berbeda dengan baris {$pertama} untuk nomor {$nomor}.";
                    }
                    if ($d['kodeMitra'] !== $acuan['kodeMitra']) {
                        $errorBaris[$nomorBaris][] = ucfirst($this->namaMitra())." berbeda dengan baris {$pertama} untuk nomor {$nomor}.";
                    }
                }

                if ($d['kodePlastik'] !== null) {
                    if (isset($plastikTerpakai[$d['kodePlastik']])) {
                        $errorBaris[$nomorBaris][] = "Data ganda: jenis plastik {$d['kodePlastik']} sudah ada pada baris {$plastikTerpakai[$d['kodePlastik']]} untuk nomor {$nomor}.";
                    } else {
                        $plastikTerpakai[$d['kodePlastik']] = $nomorBaris;
                    }
                }
            }

            // Satu baris gagal → seluruh transaksi ditolak.
            $barisGagal = $anggota->keys()->filter(fn ($n) => ! empty($errorBaris[$n]));
            if ($barisGagal->isNotEmpty()) {
                foreach ($anggota->keys() as $nomorBaris) {
                    if (empty($errorBaris[$nomorBaris])) {
                        $errorBaris[$nomorBaris][] = "Tidak diimpor karena baris lain pada nomor {$nomor} tidak valid (baris {$barisGagal->implode(', ')}).";
                    }
                }
            }
        }

        // 3) Pisahkan baris valid dan tidak valid.
        foreach ($dataBaris as $nomorBaris => $d) {
            if (! empty($errorBaris[$nomorBaris])) {
                $hasil->tambahTidakValid($nomorBaris, $baris[$nomorBaris], $errorBaris[$nomorBaris]);

                continue;
            }

            $subtotal = LayananTransaksi::subtotal(round($d['berat'], 2), round($d['harga'], 2));

            $hasil->tambahValid($nomorBaris, [
                'nomor' => $d['nomor'],
                'tanggal' => $d['tanggal']->toDateString(),
                'mitra_id' => $d['mitraId'],
                'jenis_plastik_id' => $d['plastikId'],
                'berat_kg' => round($d['berat'], 2),
                'harga_per_kg' => round($d['harga'], 2),
            ], [
                $this->kolomNomor() => $d['nomor'],
                'tanggal' => tanggal_id($d['tanggal']),
                $this->kolomKodeMitra() => $d['kodeMitra'],
                'kode_plastik' => $d['kodePlastik'],
                'berat_kg' => kg($d['berat']),
                'harga_per_kg' => rupiah($d['harga']),
                'subtotal' => rupiah($subtotal),
            ]);
        }

        return $hasil->urutkan();
    }

    public function simpan(HasilValidasi $hasil, Pengguna $pengguna): int
    {
        $layanan = app(LayananTransaksi::class);
        $jenis = $this->jenisTransaksi();

        return DB::transaction(fn () => LogAktivitas::tanpaPencatatan(function () use ($hasil, $pengguna, $layanan, $jenis) {
            $kelompok = collect($hasil->valid)->pluck('data')->groupBy('nomor');

            foreach ($kelompok as $nomor => $rincian) {
                $acuan = $rincian->first();

                $layanan->simpan($jenis, [
                    'nomor' => $nomor,
                    'tanggal' => $acuan['tanggal'],
                    'mitra_id' => $acuan['mitra_id'],
                    'pengguna_id' => $pengguna->id,
                    'sumber_data' => 'impor',
                    'catatan' => 'Diimpor dari file data offline.',
                ], $rincian->all());
            }

            return $hasil->jumlahValid();
        }));
    }
}

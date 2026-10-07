<?php

namespace App\Services\Impor;

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Dasar impor data master (supplier & pembeli): satu baris file = satu data.
 */
abstract class DefinisiMaster extends DefinisiImpor
{
    /** @return class-string<Model> */
    abstract protected function model(): string;

    abstract protected function kolomKode(): string;

    /** Aturan validasi Laravel per baris (setelah dirapikan). */
    abstract protected function aturan(): array;

    /** Merapikan nilai mentah satu baris. */
    abstract protected function rapikan(array $mentah): array;

    public function validasi(array $baris): HasilValidasi
    {
        $hasil = new HasilValidasi;
        $model = $this->model();
        $kolomKode = $this->kolomKode();

        $rapi = array_map(fn ($b) => $this->rapikan($b), $baris);

        // Data yang sudah ada di database, untuk deteksi data ganda.
        $kodeDb = $model::query()->pluck($kolomKode)->map(fn ($k) => strtoupper($k))->flip();
        $namaDb = $model::query()->pluck('nama')->map(fn ($n) => mb_strtolower($n))->flip();

        $kodeDiFile = [];
        $namaDiFile = [];

        foreach ($rapi as $nomor => $data) {
            $error = Validator::make($data, $this->aturan(), [], $this->namaAtribut())->errors()->all();

            $kode = $data[$kolomKode];
            $nama = $data['nama'] !== null ? mb_strtolower($data['nama']) : null;

            if ($kode !== null) {
                if (isset($kodeDb[$kode])) {
                    $error[] = "Data ganda: kode {$kode} sudah terdaftar di database.";
                } elseif (isset($kodeDiFile[$kode])) {
                    $error[] = "Data ganda: kode {$kode} sudah dipakai pada baris {$kodeDiFile[$kode]}.";
                } else {
                    $kodeDiFile[$kode] = $nomor;
                }
            }

            if ($nama !== null) {
                if (isset($namaDb[$nama])) {
                    $error[] = "Data ganda: nama \"{$data['nama']}\" sudah terdaftar di database.";
                } elseif (isset($namaDiFile[$nama])) {
                    $error[] = "Data ganda: nama \"{$data['nama']}\" sudah ada pada baris {$namaDiFile[$nama]}.";
                } else {
                    $namaDiFile[$nama] = $nomor;
                }
            }

            if ($error) {
                $hasil->tambahTidakValid($nomor, $baris[$nomor], $error);
            } else {
                $tampil = $data;
                $tampil[$kolomKode] ??= '(otomatis)';
                $hasil->tambahValid($nomor, $data, $tampil);
            }
        }

        return $hasil->urutkan();
    }

    public function simpan(HasilValidasi $hasil, Pengguna $pengguna): int
    {
        $model = $this->model();

        return DB::transaction(fn () => LogAktivitas::tanpaPencatatan(function () use ($hasil, $model) {
            foreach ($hasil->valid as $baris) {
                $model::create($baris['data']);
            }

            return $hasil->jumlahValid();
        }));
    }

    protected function namaAtribut(): array
    {
        return collect($this->kolom())->map(fn ($k) => mb_strtolower($k['label']))->all();
    }
}

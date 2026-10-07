<?php

namespace App\Services\Impor;

/**
 * Menampung hasil validasi: baris valid (sudah dirapikan) dan baris tidak valid beserta pesan errornya.
 */
class HasilValidasi
{
    /** @var array<int, array{data: array, tampil: array}> */
    public array $valid = [];

    /** @var array<int, array{data: array, error: string[]}> */
    public array $tidakValid = [];

    public function tambahValid(int $nomorBaris, array $data, array $tampil): void
    {
        $this->valid[$nomorBaris] = ['data' => $data, 'tampil' => $tampil];
    }

    public function tambahTidakValid(int $nomorBaris, array $dataMentah, array $error): void
    {
        $this->tidakValid[$nomorBaris] = ['data' => $dataMentah, 'error' => array_values(array_unique($error))];
    }

    public function jumlahBaris(): int
    {
        return count($this->valid) + count($this->tidakValid);
    }

    public function jumlahValid(): int
    {
        return count($this->valid);
    }

    public function jumlahTidakValid(): int
    {
        return count($this->tidakValid);
    }

    public function urutkan(): static
    {
        ksort($this->valid);
        ksort($this->tidakValid);

        return $this;
    }
}

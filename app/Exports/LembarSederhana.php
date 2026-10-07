<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Satu lembar Excel sederhana: judul kolom tebal + baris data.
 */
class LembarSederhana implements FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithStrictNullComparison, WithStyles, WithTitle
{
    /**
     * @param  int[]  $kolomTeks  indeks kolom (mulai 0) yang diformat sebagai teks
     */
    public function __construct(
        private string $judulLembar,
        private array $judul,
        private array $baris,
        private array $kolomTeks = [],
    ) {}

    public function title(): string
    {
        return $this->judulLembar;
    }

    public function headings(): array
    {
        return $this->judul;
    }

    public function array(): array
    {
        return $this->baris;
    }

    public function columnFormats(): array
    {
        $format = [];
        foreach ($this->kolomTeks as $i) {
            $format[Coordinate::stringFromColumnIndex($i + 1)] = NumberFormat::FORMAT_TEXT;
        }

        return $format;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $sheet->freezePane('A2');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '111111']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EDEBE6']],
            ],
        ];
    }
}

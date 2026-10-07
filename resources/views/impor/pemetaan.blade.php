@extends('layouts.app')

@section('judul', 'Cocokkan Kolom')

@section('konten')
<x-kepala-halaman judul="Cocokkan Kolom" :remah="['Impor Data' => route('impor.index')]"
    :deskripsi="'File '.$sesi['nama_file'].' · '.$definisi->label().' · '.angka($sesi['jumlah_baris']).' baris data'" />

@include('impor._langkah', ['aktif' => 2])

<div class="notifikasi notifikasi-info mb-4">
    <i class="ph-bold ph-info"></i>
    <div>Pasangkan setiap kolom sistem dengan kolom pada file Anda. Kolom yang namanya mirip sudah dipasangkan otomatis; periksa kembali sebelum melanjutkan.</div>
</div>

<form method="POST" action="{{ route('impor.pemetaan.simpan') }}" class="kartu" data-cegah-ganda>
    @csrf
    <div class="table-responsive">
        <table class="table tabel-data">
            <thead>
                <tr>
                    <th style="width:28%">Kolom sistem</th>
                    <th style="width:32%">Kolom di file</th>
                    <th>Contoh isi dari file</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($definisi->kolom() as $kunci => $k)
                    @php
                        $terpilih = old("pemetaan.$kunci", $sesi['pemetaan'][$kunci] ?? null);
                    @endphp
                    <tr>
                        <td>
                            <div @class(['fw-medium', 'wajib' => $k['wajib']])>{{ $k['label'] }}</div>
                            <div class="small text-redup">{{ $k['keterangan'] }}</div>
                        </td>
                        <td>
                            <select name="pemetaan[{{ $kunci }}]" data-pilih-kolom class="form-select form-select-sm @error("pemetaan.$kunci") is-invalid @enderror" aria-label="Kolom file untuk {{ $k['label'] }}">
                                <option value="">{{ $k['wajib'] ? '— Pilih kolom —' : '— Tidak diisi —' }}</option>
                                @foreach ($sesi['judul'] as $indeks => $judul)
                                    <option value="{{ $indeks }}" @selected($terpilih !== null && (string) $terpilih === (string) $indeks)>
                                        {{ $judul !== '' ? $judul : 'Kolom '.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indeks + 1).' (tanpa judul)' }}
                                    </option>
                                @endforeach
                            </select>
                            @error("pemetaan.$kunci")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </td>
                        <td class="small text-redup" data-contoh-kolom>
                            @if ($terpilih !== null && $terpilih !== '')
                                @foreach ($contoh as $baris)
                                    <span class="kode me-1">{{ \Illuminate\Support\Str::limit((string) ($baris[(int) $terpilih] ?? ''), 24) ?: '(kosong)' }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-wrap justify-content-between gap-2 px-4 py-3 border-top">
        <button type="submit" form="form-batal" class="btn btn-outline-secondary">Batalkan impor</button>
        <button type="submit" class="btn btn-dark" data-teks-proses="Memvalidasi...">Validasi data <i class="ph-bold ph-arrow-right ms-1"></i></button>
    </div>
</form>
<form method="POST" action="{{ route('impor.batal') }}" id="form-batal">@csrf</form>
@endsection

@push('scripts')
@php
    $contohPerKolom = [];
    foreach (array_keys($sesi['judul']) as $indeks) {
        $contohPerKolom[$indeks] = array_values(array_map(fn ($b) => \Illuminate\Support\Str::limit((string) ($b[$indeks] ?? ''), 24), $contoh));
    }
@endphp
<script>
    // Perbarui contoh isi saat pilihan kolom diganti.
    const contohPerKolom = @json($contohPerKolom);
    document.querySelectorAll('[data-pilih-kolom]').forEach((select) => {
        select.addEventListener('change', () => {
            const sel = select.closest('tr').querySelector('[data-contoh-kolom]');
            const nilai = contohPerKolom[select.value];
            sel.replaceChildren();
            if (!nilai) { sel.textContent = '—'; return; }
            nilai.forEach((v) => {
                const span = document.createElement('span');
                span.className = 'kode me-1';
                span.textContent = v === '' ? '(kosong)' : v;
                sel.append(span);
            });
        });
    });
</script>
@endpush

@extends('layouts.app')

@section('judul', $jenisPlastik->kode.' — '.$jenisPlastik->nama)

@section('konten')
<x-kepala-halaman :judul="$jenisPlastik->kode.' · '.$jenisPlastik->nama" :deskripsi="$jenisPlastik->keterangan" :remah="['Jenis Plastik' => route('jenis-plastik.index')]">
    <a href="{{ route('jenis-plastik.edit', $jenisPlastik) }}" class="btn btn-outline-secondary"><i class="ph ph-pencil-simple me-1"></i>Ubah</a>
    <x-tombol-hapus :aksi="route('jenis-plastik.destroy', $jenisPlastik)" :konfirmasi="'Hapus jenis plastik '.$jenisPlastik->kode.' beserta riwayat harganya?'" class="btn btn-hapus" />
</x-kepala-halaman>

<div class="row g-3 mb-4">
    <div class="col-md-4"><x-stat label="Harga beli berlaku" :nilai="$jenisPlastik->hargaBerlaku ? rupiah($jenisPlastik->hargaBerlaku->harga_beli_per_kg) : '-'" ikon="ph-tray-arrow-down" warna="biru" :keterangan="'per '.$jenisPlastik->satuan.' · dipakai untuk setoran'" /></div>
    <div class="col-md-4"><x-stat label="Harga jual berlaku" :nilai="$jenisPlastik->hargaBerlaku ? rupiah($jenisPlastik->hargaBerlaku->harga_jual_per_kg) : '-'" ikon="ph-truck" warna="hijau" :keterangan="'per '.$jenisPlastik->satuan.' · dipakai untuk penjualan'" /></div>
    <div class="col-md-4"><x-stat label="Berlaku sejak" :nilai="$jenisPlastik->hargaBerlaku ? tanggal_id($jenisPlastik->hargaBerlaku->berlaku_mulai) : '-'" ikon="ph-calendar-check" warna="kuning" :keterangan="$jenisPlastik->harga->count().' entri riwayat harga'" /></div>
</div>

<div class="kartu mb-4">
    <div class="kartu-kepala"><h2>Atur harga baru</h2><span class="small text-redup">Harga baru berlaku mulai tanggal yang dipilih. Transaksi lama tidak berubah.</span></div>
    <form method="POST" action="{{ route('jenis-plastik.harga.store', $jenisPlastik) }}" class="kartu-isi pb-2">
        @csrf
        <div class="row align-items-end">
            @include('jenis-plastik._input-harga', ['harga' => null, 'akhiran' => '_baru'])
        </div>
        <div class="mb-3"><button class="btn btn-dark" type="submit"><i class="ph ph-plus me-1"></i>Simpan harga</button></div>
    </form>
</div>

<div class="kartu">
    <div class="kartu-kepala"><h2>Riwayat harga</h2></div>
    <div class="table-responsive">
        <table class="table tabel-data">
            <thead>
                <tr>
                    <th>Berlaku mulai</th>
                    <th class="text-end">Harga beli / kg</th>
                    <th class="text-end">Harga jual / kg</th>
                    <th class="text-end">Selisih</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jenisPlastik->harga as $h)
                    <tr>
                        <td class="text-nowrap">{{ tanggal_id($h->berlaku_mulai, 'l, j F Y') }}</td>
                        <td class="text-end angka">{{ rupiah($h->harga_beli_per_kg) }}</td>
                        <td class="text-end angka">{{ rupiah($h->harga_jual_per_kg) }}</td>
                        <td class="text-end angka text-redup">{{ rupiah($h->harga_jual_per_kg - $h->harga_beli_per_kg) }}</td>
                        <td>
                            @if ($jenisPlastik->hargaBerlaku?->id === $h->id)
                                <span class="lencana lencana-hijau">berlaku</span>
                            @elseif ($h->berlaku_mulai->isFuture())
                                <span class="lencana lencana-biru">terjadwal</span>
                            @else
                                <span class="lencana">riwayat</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-outline-secondary btn-sm btn-ikon" type="button" data-bs-toggle="collapse" data-bs-target="#ubah-harga-{{ $h->id }}" title="Ubah"><i class="ph ph-pencil-simple"></i></button>
                            <x-tombol-hapus :aksi="route('jenis-plastik.harga.destroy', [$jenisPlastik, $h])" konfirmasi="Hapus entri harga ini?" label="" class="btn btn-hapus btn-sm btn-ikon" />
                        </td>
                    </tr>
                    <tr class="collapse @if(old('_harga_id') == $h->id) show @endif" id="ubah-harga-{{ $h->id }}">
                        <td colspan="6" class="bg-light-subtle">
                            <form method="POST" action="{{ route('jenis-plastik.harga.update', [$jenisPlastik, $h]) }}" class="row align-items-end pt-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_harga_id" value="{{ $h->id }}">
                                @include('jenis-plastik._input-harga', ['harga' => $h, 'akhiran' => '_'.$h->id])
                                <div class="col-12 mb-2"><button class="btn btn-dark btn-sm" type="submit">Simpan perubahan</button></div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

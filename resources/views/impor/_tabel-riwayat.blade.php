<div class="table-responsive">
    <table class="table tabel-data table-hover">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Nama file</th>
                <th>Jenis data</th>
                <th class="text-end">Baris</th>
                <th class="text-end">Berhasil</th>
                <th class="text-end">Gagal</th>
                <th>Oleh</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($log as $l)
                <tr>
                    <td class="text-nowrap">{{ tanggal_id($l->created_at, 'j M Y, H:i') }}</td>
                    <td class="text-truncate" style="max-width:240px"><i class="ph ph-file-text text-redup me-1"></i>{{ $l->nama_file }}</td>
                    <td><span class="lencana lencana-biru">{{ $l->jenis_data }}</span></td>
                    <td class="text-end angka">{{ angka($l->jumlah_baris) }}</td>
                    <td class="text-end angka" style="color:var(--pastel-hijau-teks)">{{ angka($l->jumlah_berhasil) }}</td>
                    <td class="text-end angka" @if($l->jumlah_gagal) style="color:var(--pastel-merah-teks)" @endif>{{ angka($l->jumlah_gagal) }}</td>
                    <td class="text-nowrap">{{ $l->pengguna->nama }}</td>
                    <td class="text-end"><a href="{{ route('impor.hasil', $l) }}" class="btn btn-outline-secondary btn-sm btn-ikon" title="Detail"><i class="ph ph-arrow-right"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="8"><x-kosong ikon="ph-clock-counter-clockwise" pesan="Belum ada riwayat impor." /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

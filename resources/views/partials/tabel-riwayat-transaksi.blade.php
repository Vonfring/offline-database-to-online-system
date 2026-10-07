<div class="table-responsive">
    <table class="table tabel-data table-hover">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Jenis plastik</th>
                <th class="text-end">Berat</th>
                <th class="text-end">Nilai</th>
                <th>Sumber</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $t)
                <tr>
                    <td>
                        @if (Route::has($rute))
                            <a href="{{ route($rute, $t) }}" class="kode text-decoration-none">{{ $t->{$kolomNomor} }}</a>
                        @else
                            <span class="kode">{{ $t->{$kolomNomor} }}</span>
                        @endif
                    </td>
                    <td class="text-nowrap">{{ tanggal_id($t->tanggal) }}</td>
                    <td>
                        @foreach ($t->detail as $d)
                            <span class="lencana me-1">{{ $d->jenisPlastik->kode }}</span>
                        @endforeach
                    </td>
                    <td class="text-end angka text-nowrap">{{ kg($t->total_berat_kg) }}</td>
                    <td class="text-end angka text-nowrap">{{ rupiah($t->total_nilai) }}</td>
                    <td>@include('partials.sumber-data', ['sumber' => $t->sumber_data])</td>
                </tr>
            @empty
                <tr><td colspan="6"><x-kosong ikon="ph-receipt" :pesan="$pesanKosong" /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if ($riwayat->hasPages())
    <div class="px-3 py-3 border-top">{{ $riwayat->links() }}</div>
@endif

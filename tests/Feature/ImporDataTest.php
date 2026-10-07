<?php

use App\Exports\LembarSederhana;
use App\Models\JenisPlastik;
use App\Models\LogAktivitas;
use App\Models\LogImpor;
use App\Models\Pembeli;
use App\Models\Penjualan;
use App\Models\Setoran;
use App\Models\Supplier;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Excel as JenisFile;
use Maatwebsite\Excel\Facades\Excel;

function fileCsv(string $isi, string $nama = 'data.csv'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($nama, $isi);
}

function fileXlsx(array $judul, array $baris, string $nama = 'data.xlsx'): UploadedFile
{
    $isi = Excel::raw(new LembarSederhana('Data', $judul, $baris), JenisFile::XLSX);

    return UploadedFile::fake()->createWithContent($nama, $isi);
}

function siapkanPlastik(): void
{
    foreach (['PET' => [5000, 7300], 'HDPE' => [5600, 8000]] as $kode => [$beli, $jual]) {
        JenisPlastik::create(['kode' => $kode, 'nama' => $kode, 'satuan' => 'kg'])
            ->harga()->create(['harga_beli_per_kg' => $beli, 'harga_jual_per_kg' => $jual, 'berlaku_mulai' => '2026-01-01']);
    }
}

test('template impor dapat diunduh untuk setiap jenis data', function (string $jenis) {
    $this->actingAs(admin())->get("/impor/template/{$jenis}")
        ->assertOk()
        ->assertDownload("template-impor-{$jenis}.xlsx");
})->with(['supplier', 'pembeli', 'setoran', 'penjualan']);

test('alur impor supplier dari CSV: unggah, cocokkan kolom, pratinjau, konfirmasi', function () {
    $admin = admin();
    Supplier::factory()->create(['kode_supplier' => 'SUP-0050', 'nama' => 'Sudah Ada']);

    $csv = "Kode Supplier;Nama Supplier;Kategori;No HP;Alamat\n"
        ."SUP-0101;Bank Sampah Mawar;pengelola sampah;81234567890;Bekasi\n"   // valid (0 di depan HP dipulihkan)
        ."SUP-0102;Pengepul Pak Darto;PENGUMPUL;;Tambun\n"                    // valid (kategori dirapikan)
        .";Tanpa Kode;umkm;;\n"                                              // valid (kode otomatis)
        ."SUP-0103;;pengumpul;;\n"                                           // nama kosong
        ."SUP-0101;Nama Lain;pengumpul;;\n"                                  // kode ganda di file
        ."SUP-0050;Supplier Baru;pengumpul;;\n"                              // kode sudah ada di database
        ."SUP-0104;Pengepul X;tukang loak;;\n"                               // kategori tidak valid
        .";;;;\n";                                                           // baris kosong diabaikan

    // 1. Unggah
    $this->actingAs($admin)->post('/impor/unggah', ['jenis_data' => 'supplier', 'file' => fileCsv($csv, 'supplier-offline.csv')])
        ->assertRedirect('/impor/pemetaan');

    // 2. Pemetaan otomatis terdeteksi
    $this->get('/impor/pemetaan')->assertOk()->assertSee('Kode Supplier')->assertSee('Nama Supplier');
    expect(session('impor.pemetaan'))->toBe(['kode_supplier' => 0, 'nama' => 1, 'kategori' => 2, 'no_hp' => 3, 'alamat' => 4]);

    $this->post('/impor/pemetaan', ['pemetaan' => session('impor.pemetaan')])->assertRedirect('/impor/pratinjau');

    // 3. Pratinjau: 3 valid, 4 tidak valid
    $this->get('/impor/pratinjau')->assertOk()
        ->assertSee('Nama wajib diisi.')
        ->assertSee('Data ganda: kode SUP-0101 sudah dipakai pada baris 2.')
        ->assertSee('Data ganda: kode SUP-0050 sudah terdaftar di database.')
        ->assertSee('Kategori yang dipilih tidak valid.');

    expect(Supplier::count())->toBe(1); // belum ada yang disimpan

    // 4. Konfirmasi
    $respon = $this->post('/impor/konfirmasi');
    $log = LogImpor::sole();
    $respon->assertRedirect("/impor/riwayat/{$log->id}");

    expect($log->only(['nama_file', 'jenis_data', 'jumlah_baris', 'jumlah_berhasil', 'jumlah_gagal', 'pengguna_id']))->toBe([
        'nama_file' => 'supplier-offline.csv',
        'jenis_data' => 'supplier',
        'jumlah_baris' => 7,
        'jumlah_berhasil' => 3,
        'jumlah_gagal' => 4,
        'pengguna_id' => $admin->id,
    ]);

    expect(Supplier::count())->toBe(4)
        ->and(Supplier::firstWhere('kode_supplier', 'SUP-0101')->no_hp)->toBe('081234567890')
        ->and(Supplier::firstWhere('kode_supplier', 'SUP-0102')->kategori)->toBe('pengumpul')
        ->and(Supplier::firstWhere('nama', 'Tanpa Kode')->kode_supplier)->toStartWith('SUP-')
        ->and(Supplier::firstWhere('nama', 'Tanpa Kode')->kategori)->toBe('UMKM');

    // Impor massal tidak membanjiri log aktivitas (sudah tercatat di log_impor).
    expect(LogAktivitas::count())->toBe(0);
    expect(session('impor'))->toBeNull();

    $this->get("/impor/riwayat/{$log->id}")->assertOk()->assertSee('supplier-offline.csv');
    $this->get('/impor/riwayat')->assertOk()->assertSee('supplier-offline.csv');
});

test('kolom wajib harus dipetakan', function () {
    $this->actingAs(admin())->post('/impor/unggah', [
        'jenis_data' => 'pembeli',
        'file' => fileCsv("Kolom A,Kolom B\nx,y\n"),
    ])->assertRedirect('/impor/pemetaan');

    $this->post('/impor/pemetaan', ['pemetaan' => ['nama' => '']])
        ->assertSessionHasErrors('pemetaan.nama');

    $this->post('/impor/pemetaan', ['pemetaan' => ['nama' => 0, 'perusahaan' => 0]])
        ->assertSessionHas('gagal');
});

test('file dengan format salah atau kosong ditolak', function () {
    $admin = admin();

    $this->actingAs($admin)->post('/impor/unggah', [
        'jenis_data' => 'supplier',
        'file' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
    ])->assertSessionHasErrors('file');

    $this->actingAs($admin)->post('/impor/unggah', [
        'jenis_data' => 'supplier',
        'file' => fileCsv("Kode Supplier,Nama\n"),
    ])->assertSessionHas('gagal', 'File hanya berisi judul kolom tanpa data.');
});

test('impor setoran dari Excel menggabungkan baris per nomor dan memakai harga yang berlaku', function () {
    siapkanPlastik();
    Supplier::factory()->create(['kode_supplier' => 'SUP-0001']);
    Supplier::factory()->create(['kode_supplier' => 'SUP-0002']);

    $file = fileXlsx(
        ['No Setoran', 'Tanggal', 'Kode Supplier', 'Kode Plastik', 'Berat (kg)', 'Harga per kg'],
        [
            ['ST-001', '2026-06-15', 'SUP-0001', 'PET', 100.5, null],      // harga otomatis 5000
            ['ST-001', '15/06/2026', 'sup-0001', 'HDPE', '20,25', 6000],    // harga dari file
            ['ST-002', '2026-06-16', 'SUP-0002', 'PET', 50, null],          // valid
            ['ST-003', '2026-06-17', 'SUP-0002', 'PET', 10, null],          // valid, tetapi...
            ['ST-003', '2026-06-17', 'SUP-0002', 'XYZ', 5, null],           // ...plastik tidak dikenal → ST-003 ditolak
            ['ST-004', '2026-06-18', 'SUP-9999', 'PET', 10, null],          // supplier tidak terdaftar
            ['ST-005', '2099-01-01', 'SUP-0001', 'PET', 10, null],          // tanggal masa depan
            ['ST-006', '2026-06-19', 'SUP-0001', 'PET', 0, null],           // berat 0
            ['ST-007', '2026-06-20', 'SUP-0001', 'PET', 10, null],
            ['ST-007', '2026-06-20', 'SUP-0001', 'PET', 12, null],          // plastik ganda dalam satu nomor
        ],
        'setoran-offline.xlsx'
    );

    $this->actingAs(admin())->post('/impor/unggah', ['jenis_data' => 'setoran', 'file' => $file]);
    $this->post('/impor/pemetaan', ['pemetaan' => session('impor.pemetaan')])->assertRedirect('/impor/pratinjau');

    $this->get('/impor/pratinjau')->assertOk()
        ->assertSee('Kode jenis plastik XYZ tidak terdaftar.')
        ->assertSee('Tidak diimpor karena baris lain pada nomor ST-003 tidak valid')
        ->assertSee('Kode supplier SUP-9999 tidak terdaftar.')
        ->assertSee('Tanggal tidak boleh melebihi hari ini.')
        ->assertSee('Berat (kg) harus lebih dari 0.')
        ->assertSee('Data ganda: jenis plastik PET sudah ada pada baris 10 untuk nomor ST-007.');

    $this->post('/impor/konfirmasi')->assertRedirect();

    $log = LogImpor::sole();
    expect([$log->jumlah_baris, $log->jumlah_berhasil, $log->jumlah_gagal])->toBe([10, 3, 7]);

    expect(Setoran::count())->toBe(2);

    $st1 = Setoran::with('detail.jenisPlastik')->firstWhere('no_setoran', 'ST-001');
    expect($st1->sumber_data)->toBe('impor')
        ->and($st1->tanggal->toDateString())->toBe('2026-06-15')
        ->and($st1->detail)->toHaveCount(2)
        ->and((float) $st1->total_berat_kg)->toBe(120.75)
        ->and((float) $st1->total_nilai)->toBe(100.5 * 5000 + 20.25 * 6000);

    $pet = $st1->detail->firstWhere('jenisPlastik.kode', 'PET');
    expect((float) $pet->harga_per_kg)->toBe(5000.0)
        ->and((float) $pet->subtotal)->toBe(502500.0);
});

test('nomor transaksi yang sudah ada di database ditolak sebagai data ganda', function () {
    siapkanPlastik();
    Pembeli::factory()->create(['kode_pembeli' => 'PBL-0001']);

    $csv = "No Penjualan,Tanggal,Kode Pembeli,Kode Plastik,Berat (kg),Harga per kg\nJL-001,2026-06-20,PBL-0001,PET,1000,\n";

    $this->actingAs(admin());
    foreach ([1, 2] as $percobaan) {
        $this->post('/impor/unggah', ['jenis_data' => 'penjualan', 'file' => fileCsv($csv)]);
        $this->post('/impor/pemetaan', ['pemetaan' => session('impor.pemetaan')]);

        if ($percobaan === 1) {
            $this->post('/impor/konfirmasi');
        }
    }

    $this->get('/impor/pratinjau')->assertSee('Data ganda: nomor JL-001 sudah ada di database.');
    $this->post('/impor/konfirmasi')->assertSessionHas('gagal');

    expect(Penjualan::count())->toBe(1)
        ->and((float) Penjualan::first()->total_nilai)->toBe(7300000.0); // harga jual otomatis
});

test('unggah ulang file perbaikan memakai pemetaan kolom sebelumnya', function () {
    $this->actingAs(admin());
    $judul = "Nama,Perusahaan\n";

    $this->post('/impor/unggah', ['jenis_data' => 'pembeli', 'file' => fileCsv($judul.",PT Tanpa Nama\n")]);
    $this->post('/impor/pemetaan', ['pemetaan' => session('impor.pemetaan')]);
    $this->get('/impor/pratinjau')->assertSee('Nama wajib diisi.');

    $this->post('/impor/unggah', ['jenis_data' => 'pembeli', 'file' => fileCsv($judul."Rudi,PT Tanpa Nama\n")])
        ->assertRedirect('/impor/pratinjau');

    $this->get('/impor/pratinjau')->assertDontSee('Nama wajib diisi.');
    $this->post('/impor/konfirmasi');

    expect(Pembeli::firstWhere('nama', 'Rudi')?->perusahaan)->toBe('PT Tanpa Nama');
});

test('impor dapat dibatalkan tanpa menyimpan data', function () {
    $this->actingAs(admin())->post('/impor/unggah', ['jenis_data' => 'pembeli', 'file' => fileCsv("Nama\nRudi\n")]);
    $this->post('/impor/batal')->assertRedirect('/impor');

    expect(session('impor'))->toBeNull()
        ->and(Pembeli::count())->toBe(0)
        ->and(LogImpor::count())->toBe(0);
});

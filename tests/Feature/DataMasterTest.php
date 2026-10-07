<?php

use App\Models\HargaPlastik;
use App\Models\JenisPlastik;
use App\Models\LogAktivitas;
use App\Models\Pembeli;
use App\Models\Supplier;

test('staf dapat menambah supplier dengan kode otomatis dan tercatat di log aktivitas', function () {
    $staf = staf();

    $this->actingAs($staf)->post('/supplier', [
        'nama' => 'Bank Sampah Mawar',
        'kategori' => 'pengelola sampah',
        'no_hp' => '081234567890',
    ])->assertRedirect()->assertSessionHasNoErrors();

    $supplier = Supplier::firstWhere('nama', 'Bank Sampah Mawar');
    expect($supplier->kode_supplier)->toBe('SUP-0001');

    expect(LogAktivitas::where(['nama_tabel' => 'supplier', 'aksi' => 'tambah', 'data_id' => $supplier->id, 'pengguna_id' => $staf->id])->exists())->toBeTrue();
});

test('kode supplier harus unik dan kategori harus valid', function () {
    Supplier::factory()->create(['kode_supplier' => 'SUP-0100']);

    $this->actingAs(staf())->post('/supplier', [
        'kode_supplier' => 'sup-0100',
        'nama' => 'Supplier Lain',
        'kategori' => 'tidak ada',
    ])->assertSessionHasErrors(['kode_supplier', 'kategori']);
});

test('pencarian supplier menyaring daftar', function () {
    Supplier::factory()->create(['nama' => 'Pengepul Pak Slamet']);
    Supplier::factory()->create(['nama' => 'Bank Sampah Melati']);

    $this->actingAs(staf())->get('/supplier?q=slamet')
        ->assertSee('Pengepul Pak Slamet')
        ->assertDontSee('Bank Sampah Melati');
});

test('staf dapat mengubah dan menghapus pembeli', function () {
    $pembeli = Pembeli::factory()->create();

    $this->actingAs(staf())->put("/pembeli/{$pembeli->id}", [
        'kode_pembeli' => $pembeli->kode_pembeli,
        'nama' => 'Nama Diperbarui',
        'perusahaan' => 'PT Baru',
    ])->assertRedirect("/pembeli/{$pembeli->id}");

    expect($pembeli->refresh()->nama)->toBe('Nama Diperbarui');

    $this->actingAs(staf())->delete("/pembeli/{$pembeli->id}")->assertRedirect('/pembeli');
    expect(Pembeli::count())->toBe(0);
});

test('admin dapat menambah jenis plastik beserta harga awal', function () {
    $this->actingAs(admin())->post('/jenis-plastik', [
        'kode' => 'pp',
        'nama' => 'Polypropylene',
        'satuan' => 'kg',
        'harga_beli_per_kg' => 4000,
        'harga_jual_per_kg' => 6000,
        'berlaku_mulai' => '2026-09-01',
    ])->assertRedirect()->assertSessionHasNoErrors();

    $jenis = JenisPlastik::firstWhere('kode', 'PP');
    expect($jenis)->not->toBeNull()
        ->and($jenis->harga()->count())->toBe(1);
});

test('harga yang berlaku diambil dari tanggal berlaku terbaru yang tidak melewati tanggal transaksi', function () {
    $jenis = JenisPlastik::create(['kode' => 'PET', 'nama' => 'PET', 'satuan' => 'kg']);
    $jenis->harga()->createMany([
        ['harga_beli_per_kg' => 4800, 'harga_jual_per_kg' => 7000, 'berlaku_mulai' => '2026-06-01'],
        ['harga_beli_per_kg' => 5000, 'harga_jual_per_kg' => 7300, 'berlaku_mulai' => '2026-08-01'],
    ]);

    expect(HargaPlastik::berlakuPada($jenis->id, '2026-05-31'))->toBeNull()
        ->and((float) HargaPlastik::berlakuPada($jenis->id, '2026-07-31')->harga_beli_per_kg)->toBe(4800.0)
        ->and((float) HargaPlastik::berlakuPada($jenis->id, '2026-08-01')->harga_beli_per_kg)->toBe(5000.0);
});

test('tanggal berlaku harga tidak boleh ganda untuk jenis plastik yang sama', function () {
    $jenis = JenisPlastik::create(['kode' => 'PET', 'nama' => 'PET', 'satuan' => 'kg']);
    $jenis->harga()->create(['harga_beli_per_kg' => 4800, 'harga_jual_per_kg' => 7000, 'berlaku_mulai' => '2026-06-01']);

    $this->actingAs(admin())->post("/jenis-plastik/{$jenis->id}/harga", [
        'harga_beli_per_kg' => 5000, 'harga_jual_per_kg' => 7200, 'berlaku_mulai' => '2026-06-01',
    ])->assertSessionHasErrors('berlaku_mulai');
});

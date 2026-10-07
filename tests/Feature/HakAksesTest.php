<?php

/*
| Staf: dashboard, data master supplier/pembeli, transaksi, laporan.
| Admin: semua hak staf + jenis plastik & harga, impor, pengaturan.
*/

dataset('halaman_staf', [
    '/dashboard', '/supplier', '/supplier/create', '/pembeli', '/pembeli/create', '/profil',
]);

dataset('halaman_admin', [
    '/jenis-plastik', '/jenis-plastik/create', '/impor', '/impor/riwayat', '/impor/template/supplier',
]);

test('staf dapat membuka halaman staf', function (string $url) {
    $this->actingAs(staf())->get($url)->assertOk();
})->with('halaman_staf');

test('admin mewarisi semua hak akses staf', function (string $url) {
    $this->actingAs(admin())->get($url)->assertOk();
})->with('halaman_staf');

test('staf ditolak di halaman khusus admin', function (string $url) {
    $this->actingAs(staf())->get($url)->assertForbidden();
})->with('halaman_admin');

test('admin dapat membuka halaman khusus admin', function (string $url) {
    $this->actingAs(admin())->get($url)->assertOk();
})->with('halaman_admin');

test('staf tidak dapat mengunggah file impor', function () {
    $this->actingAs(staf())->post('/impor/unggah', ['jenis_data' => 'supplier'])->assertForbidden();
});

test('menu khusus admin tidak tampil untuk staf', function () {
    $this->actingAs(staf())->get('/dashboard')
        ->assertDontSee('Impor Data')
        ->assertDontSee('Jenis Plastik &amp; Harga', false);

    $this->actingAs(admin())->get('/dashboard')
        ->assertSee('Impor Data');
});

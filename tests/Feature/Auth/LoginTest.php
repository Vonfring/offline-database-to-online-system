<?php

use App\Models\Pengguna;

test('halaman login dapat dibuka', function () {
    $this->get('/masuk')->assertOk()->assertSee('Masuk');
});

test('tamu diarahkan ke halaman login', function () {
    $this->get('/dashboard')->assertRedirect('/masuk');
});

test('admin dapat login dan diarahkan ke dashboard', function () {
    $admin = admin();

    $this->post('/masuk', ['email' => $admin->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('staf dapat login dan diarahkan ke dashboard', function () {
    $staf = staf();

    $this->post('/masuk', ['email' => $staf->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($staf);
    $this->get('/dashboard')->assertOk()->assertSee($staf->nama);
});

test('login gagal dengan kata sandi salah', function () {
    $staf = staf();

    $this->post('/masuk', ['email' => $staf->email, 'password' => 'salah'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('akun nonaktif tidak bisa login', function () {
    $pengguna = Pengguna::factory()->nonaktif()->create();

    $this->post('/masuk', ['email' => $pengguna->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('akun yang dinonaktifkan saat sesi berjalan langsung dikeluarkan', function () {
    $pengguna = staf();
    $this->actingAs($pengguna);
    $pengguna->update(['aktif' => false]);

    $this->get('/dashboard')->assertRedirect('/masuk');
    $this->assertGuest();
});

test('pengguna dapat logout', function () {
    $this->actingAs(staf())->post('/keluar')->assertRedirect('/masuk');

    $this->assertGuest();
});

test('pengguna dapat memperbarui profil dan kata sandi', function () {
    $staf = staf();

    $this->actingAs($staf)->patch('/profil', ['nama' => 'Nama Baru', 'email' => 'baru@fathoni.test'])
        ->assertRedirect('/profil')->assertSessionHasNoErrors();

    $this->actingAs($staf)->from('/profil')->put('/kata-sandi', [
        'current_password' => 'password',
        'password' => 'sandi-baru-123',
        'password_confirmation' => 'sandi-baru-123',
    ])->assertRedirect('/profil')->assertSessionHasNoErrors();

    $staf->refresh();
    expect($staf->nama)->toBe('Nama Baru')
        ->and(Hash::check('sandi-baru-123', $staf->password))->toBeTrue();
});

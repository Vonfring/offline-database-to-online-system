<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_impor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->restrictOnDelete();
            $table->string('nama_file', 255);
            $table->string('jenis_data', 30);
            $table->integer('jumlah_baris');
            $table->integer('jumlah_berhasil');
            $table->integer('jumlah_gagal');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->restrictOnDelete();
            $table->string('aksi', 20); // tambah / ubah / hapus
            $table->string('nama_tabel', 50);
            $table->unsignedBigInteger('data_id');
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('log_impor');
    }
};

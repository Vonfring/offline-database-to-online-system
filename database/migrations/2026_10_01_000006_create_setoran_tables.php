<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->string('no_setoran', 30)->unique();
            $table->date('tanggal')->index();
            $table->foreignId('supplier_id')->constrained('supplier')->restrictOnDelete();
            $table->foreignId('pengguna_id')->constrained('pengguna')->restrictOnDelete();
            $table->decimal('total_berat_kg', 12, 2)->default(0);
            $table->decimal('total_nilai', 14, 2)->default(0);
            $table->string('sumber_data', 10)->default('input'); // input / impor
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setoran_id')->constrained('setoran')->cascadeOnDelete();
            $table->foreignId('jenis_plastik_id')->constrained('jenis_plastik')->restrictOnDelete();
            $table->decimal('berat_kg', 12, 2);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('subtotal', 14, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_setoran');
        Schema::dropIfExists('setoran');
    }
};

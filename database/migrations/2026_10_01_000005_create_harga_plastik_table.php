<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_plastik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_plastik_id')->constrained('jenis_plastik')->cascadeOnDelete();
            $table->decimal('harga_beli_per_kg', 12, 2);
            $table->decimal('harga_jual_per_kg', 12, 2);
            $table->date('berlaku_mulai');

            // Satu jenis plastik hanya boleh punya satu harga per tanggal berlaku.
            $table->unique(['jenis_plastik_id', 'berlaku_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_plastik');
    }
};

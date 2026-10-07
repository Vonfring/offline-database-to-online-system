<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_plastik', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // PET / HDPE / LDPE / ABS
            $table->string('nama', 100);
            $table->string('satuan', 10)->default('kg');
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_plastik');
    }
};

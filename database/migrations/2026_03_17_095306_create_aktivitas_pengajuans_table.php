<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aktivitas_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('KodePengajuan')->nullable();
            $table->string('Jenis')->nullable();
            $table->string('Keterangan')->nullable();
            $table->string('UserCreate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_pengajuans');
    }
};

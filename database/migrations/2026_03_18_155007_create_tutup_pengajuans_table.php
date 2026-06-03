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
        Schema::create('tutup_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('Nama')->nullable();
            $table->dateTime('TanggalMulai')->nullable();
            $table->dateTime('TanggalSelesai')->nullable();
            $table->text('Keterangan')->nullable();
            $table->enum('isAktif', ['Y', 'N'])->nullable();
            $table->string('UserCreate')->nullable();
            $table->string('UserUpdate')->nullable();
            $table->string('UserDelete')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutup_pengajuans');
    }
};

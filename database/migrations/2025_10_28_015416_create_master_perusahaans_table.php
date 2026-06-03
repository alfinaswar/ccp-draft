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
        Schema::create('master_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('Kode')->nullable();
            $table->string('Nama')->nullable();
            $table->string('NamaLengkap')->nullable();
            $table->string('Deskripsi')->nullable();
            $table->string('NominalRkap')->nullable();
            $table->string('SisaRkap')->nullable();
            $table->enum('Kategori', ['ABGROUP', 'CISCO'])->nullable();
            $table->string('Koneksi')->nullable();
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
        Schema::dropIfExists('master_perusahaans');
    }
};

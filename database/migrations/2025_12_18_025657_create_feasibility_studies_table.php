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
        Schema::create('feasibility_studies', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->string('NamaBarang')->nullable();
            $table->string('NilaiInvestasi')->nullable();
            $table->string('Spesifikasi')->nullable();
            $table->string('BiayaTetap')->nullable();
            $table->string('BungaTetap')->nullable();
            $table->string('Penyusutan')->nullable();
            $table->string('Maintenance')->nullable();
            $table->string('Pegawai')->nullable();
            $table->string('SewaGedung')->nullable();
            $table->string('TotalBiayaTetap')->nullable();
            $table->string('Konsumable')->nullable();
            $table->string('Dokter')->nullable();
            $table->string('TotalBiayaVariable')->nullable();
            $table->string('Tarif')->nullable();
            $table->string('KodePerusahaan')->nullable();
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
        Schema::dropIfExists('feasibility_studies');
    }
};

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
        Schema::create('pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->string('IdPengajuan')->nullable();
            $table->string('IdBarang')->nullable();
            $table->string('RencanaPenempatan')->nullable();
            $table->string('DiajukanOleh')->nullable();
            $table->string('DiajukanDepartemen')->nullable();
            $table->string('Jumlah')->nullable();
            $table->string('Satuan')->nullable();
            $table->string('VendorAcc')->nullable();
            $table->string('HargaSatuanAcc')->nullable();
            $table->string('HargaNegoAcc')->nullable();
            $table->string('HargaAkhirFui')->nullable();
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
        Schema::dropIfExists('pengajuan_items');
    }
};

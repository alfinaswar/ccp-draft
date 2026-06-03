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
        Schema::create('permintaan_pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->string('IdPermintaan')->nullable();
            $table->string('NamaBarang')->nullable();
            $table->string('Jumlah')->nullable();
            $table->string('Satuan')->nullable();
            $table->string('RencanaPenempatan')->nullable();
            $table->string('Keterangan')->nullable();
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
        Schema::dropIfExists('permintaan_pembelian_details');
    }
};

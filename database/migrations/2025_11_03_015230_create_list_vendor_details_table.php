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
        Schema::create('list_vendor_details', function (Blueprint $table) {
            $table->id();
            $table->string('IdPengajuan')->nullable();
            $table->string('IdListVendor')->nullable();
            $table->string('NamaBarang')->nullable();
            $table->string('NamaVendor')->nullable();
            $table->string('Jumlah')->nullable();
            $table->string('HargaSatuan')->nullable();
            $table->string('HargaNego')->nullable();
            $table->string('Diskon')->nullable();
            $table->string('JenisDiskon')->nullable();
            $table->string('TotalDiskon')->nullable();
            $table->string('TotalHarga')->nullable();
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
        Schema::dropIfExists('list_vendor_details');
    }
};

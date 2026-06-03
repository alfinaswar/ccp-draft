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
        Schema::create('list_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('IdPengajuan')->nullable();
            $table->string('VendorKe')->nullable();
            $table->string('NamaVendor')->nullable();
            $table->string('Hta')->nullable();
            $table->string('SuratPenawaranVendor')->nullable();
            $table->string('HargaTanpaDiskon')->nullable();
            $table->string('HargaDenganDiskon')->nullable();
            $table->string('TotalDiskon')->nullable();
            $table->string('Ppn')->nullable();
            $table->string('TotalPpn')->nullable();
            $table->string('TotalHarga')->nullable();
            $table->enum('AccVendor', ['Y', 'N'])->nullable();
            $table->dateTime('AccVendorPada')->nullable();
            $table->dateTime('AccVendorOleh')->nullable();
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
        Schema::dropIfExists('list_vendors');
    }
};

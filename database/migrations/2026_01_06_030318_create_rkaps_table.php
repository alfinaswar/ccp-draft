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
        Schema::create('rkaps', function (Blueprint $table) {
            $table->id();
            $table->string('PerusahaanId')->nullable();
            $table->string('Tahun')->nullable();
            $table->enum('Jenis', ['UMUM', 'MEDIS'])->nullable();
            $table->string('NominalRkap')->nullable();
            $table->string('SisaRkap')->nullable();
            $table->string('NominalRkapUmum')->nullable();
            $table->string('SisaRkapUmum')->nullable();
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
        Schema::dropIfExists('rkaps');
    }
};

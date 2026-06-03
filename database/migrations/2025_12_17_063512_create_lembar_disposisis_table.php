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
        Schema::create('lembar_disposisis', function (Blueprint $table) {
            $table->id();
            $table->string('JenisForm')->nullable();
            $table->string('IdPengajuan')->nullable();
            $table->string('PengajuanItemId')->nullable();
            $table->string('NamaBarang')->nullable();
            $table->string('Harga')->nullable();
            $table->string('RencanaVendor')->nullable();
            $table->string('TujuanPenempatan')->nullable();
            $table->enum('FormPermintaan', ['Y', 'N'])->nullable()->default('Y');
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
        Schema::dropIfExists('lembar_disposisis');
    }
};

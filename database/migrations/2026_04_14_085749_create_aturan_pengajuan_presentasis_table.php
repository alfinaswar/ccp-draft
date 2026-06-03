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
        Schema::create('aturan_pengajuan_presentasis', function (Blueprint $table) {
            $table->id();
            $table->string('KodeHari', 100)->nullable();
            $table->string('NamaHari', 100)->nullable();
            $table->time('JamMulai')->nullable();
            $table->time('JamSelesai')->nullable();
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
        Schema::dropIfExists('aturan_pengajuan_presentasis');
    }
};

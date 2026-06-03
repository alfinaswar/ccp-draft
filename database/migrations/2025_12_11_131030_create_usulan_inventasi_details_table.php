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
        Schema::create('usulan_investasi_details', function (Blueprint $table) {
            $table->id();
            $table->string('IdUsulan')->nullable();
            $table->string('Vendor')->nullable();
            $table->string('NamaBarang')->nullable();
            $table->string('Jumlah')->nullable();
            $table->string('Harga')->nullable();
            $table->string('Diskon')->nullable();
            $table->string('Ppn')->nullable();
            $table->string('Total')->nullable();
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
        Schema::dropIfExists('usulan_inventasi_details');
    }
};

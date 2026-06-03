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
        Schema::create('feasibility_study_details', function (Blueprint $table) {
            $table->id();
            $table->string('IdFs')->nullable();
            $table->string('TahunKe')->nullable();
            $table->string('JumlahPasien')->nullable();
            $table->string('TarifUmum')->nullable();
            $table->string('TarifBpjs')->nullable();

            // Kolom Kalkulasi (Disimpan agar memudahkan reporting)
            $table->string('Revenue')->nullable();
            $table->string('BiayaTetap')->nullable();
            $table->string('BiayaVariable')->nullable();
            $table->string('NetProfit')->nullable();
            $table->string('Ebitda')->nullable();
            $table->string('AkumEbitda')->nullable();
            $table->string('RoiTahunKe')->nullable();
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
        Schema::dropIfExists('feasibility_study_details');
    }
};

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
        Schema::table('feasibility_study_details', function (Blueprint $table) {
            $table->string('JumlahPasienBpjs')->nullable()->after('JumlahPasien');
            $table->string('TotalBiaya')->nullable()->after('BiayaVariable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feasibility_study_details', function (Blueprint $table) {
            //
        });
    }
};

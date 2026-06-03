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
        Schema::table('master_perusahaans', function (Blueprint $table) {
            $table->string('NominalRkapUmum')->nullable()->after('SisaRkap');
            $table->string('SisaRkapUmum')->nullable()->after('NominalRkapUmum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_perusahaans', function (Blueprint $table) {
            //
        });
    }
};

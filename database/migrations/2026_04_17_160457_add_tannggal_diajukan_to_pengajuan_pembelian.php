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
        Schema::table('pengajuan_pembelians', function (Blueprint $table) {
            $table->dateTime('DiajukanPadaRekomendasi')->nullable()->after('DiajukanPada');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_pembelians', function (Blueprint $table) {
            //
        });
    }
};

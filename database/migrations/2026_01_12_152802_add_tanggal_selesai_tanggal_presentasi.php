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
            $table->date('TanggalPresentasi')->nullable()->after('DiajukanPada');
            $table->enum('StatusPresent', ['Y', 'N'])->nullable()->after('TanggalPresentasi');
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

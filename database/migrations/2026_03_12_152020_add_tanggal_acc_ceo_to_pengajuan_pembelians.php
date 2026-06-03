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
            $table->enum('AccCeo', ['Y', 'N'])->nullable()->after('TanggalPresentasi');
            $table->dateTime('TanggalAccCeo')->nullable()->after('AccCeo');
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

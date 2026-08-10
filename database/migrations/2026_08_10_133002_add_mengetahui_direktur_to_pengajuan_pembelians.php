<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_pembelians', function (Blueprint $table) {
            $table->string('AccDirektur')->nullable()->after('DisetujuiPada');
            $table->string('DirekturId')->nullable()->after('AccDirektur');
            $table->dateTime('AccDirekturPada')->nullable()->after('DirekturId');
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

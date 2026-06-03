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
        Schema::table('feasibility_studies', function (Blueprint $table) {
            $table->string('BungaBank')->nullable()->after('Tarif');
            $table->string('EstimasiPembiayaan')->nullable()->after('BungaBank');
            $table->string('UmurEkonomis')->nullable()->after('EstimasiPembiayaan');
            $table->string('Maintenance2')->nullable()->after('UmurEkonomis');
            $table->string('JumlahPerHariPakai')->nullable()->after('Maintenance');
            $table->string('JumlahAlat')->nullable()->after('JumlahPerHariPakai');
            $table->string('JumlahPakaiPertahun')->nullable()->after('JumlahAlat');
            $table->string('JumlahHariRawat')->nullable()->after('JumlahPakaiPertahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feasibility_studies', function (Blueprint $table) {
            //
        });
    }
};

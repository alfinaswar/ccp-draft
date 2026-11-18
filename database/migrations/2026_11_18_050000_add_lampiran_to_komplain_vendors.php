<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('komplain_vendors', function (Blueprint $table) {
            // [{path, nama, tipe, ukuran}] — foto atau PDF bukti pendukung
            $table->json('Lampiran')->nullable()->after('DataPendukungLainnya');
        });
    }

    public function down(): void
    {
        Schema::table('komplain_vendors', function (Blueprint $table) {
            $table->dropColumn('Lampiran');
        });
    }
};

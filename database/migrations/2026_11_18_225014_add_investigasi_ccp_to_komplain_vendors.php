<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('komplain_vendors', function (Blueprint $table) {
            $table->longText('KeteranganCcp')->nullable()->after('TindakanCcpLainnya');
            $table->json('LampiranCcp')->nullable()->after('KeteranganCcp');
        });
    }

    public function down(): void
    {
        Schema::table('komplain_vendors', function (Blueprint $table) {
            $table->dropColumn(['KeteranganCcp', 'LampiranCcp']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_units', function (Blueprint $table) {
            // null = unit berlaku untuk semua perusahaan
            $table->string('KodePerusahaan')->nullable()->after('parent_id');
            $table->index('KodePerusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('master_units', function (Blueprint $table) {
            $table->dropIndex(['KodePerusahaan']);
            $table->dropColumn('KodePerusahaan');
        });
    }
};

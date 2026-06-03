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
        Schema::table('list_vendors', function (Blueprint $table) {
            $table->string('NamaPic', 200)->nullable()->after('VendorKe');
            $table->string('KontakPic', 200)->nullable()->after('NamaPic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_vendors', function (Blueprint $table) {
            //
        });
    }
};

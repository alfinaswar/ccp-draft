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
        Schema::table('lembar_disposisis', function (Blueprint $table) {
            $table->enum('Status', ['Draft', 'Final'])->nullable()->after('FormPermintaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembar_disposisis', function (Blueprint $table) {
            //
        });
    }
};
